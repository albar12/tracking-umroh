function setSelections(_el, _url, _params = {}, _selected = false) {
    const _items = [];

    if (!_el || !$(_el).length) return _items;

    const $select = $(_el);
    const currentValue = $select.val();
    const isMultiple =
        $select.hasClass("select2-multiple") || $select.hasClass("multi-select");

    // Reset nilai sebelumnya
    if (isMultiple && currentValue?.length > 0) {
        $select.val(null).trigger("change");
    } else if (currentValue) {
        $select.val(null).trigger("change");
    }

    $select.empty(); // Bersihkan semua option
    if (!isMultiple) {
        $select.append('<option value="">-- Pilih --</option>');
    }

    if (_url) {
        $.ajax({
            type: "POST",
            url: _url, // Harus sudah berupa site_url lengkap, jangan hardcode di sini
            data: _params,
            dataType: "json",
            cache: false,
            async: false,
            beforeSend: function () {
                if (typeof loading_open === "function") loading_open();
            },
            success: function (response) {
                if (response.items && Array.isArray(response.items)) {
                    setItems(_el, response.items, _selected);
                } else {
                    console.warn("Data 'items' tidak ditemukan.");
                }
            },
            error: function (xhr, status, error) {
                Swal.fire("Error", error || "Gagal mengambil data.", "error");
            },
            complete: function () {
                if (typeof loading_close === "function") loading_close();
            },
        });
    }

    return _items;
}

function loading_open() {
    $("#loading").show();
}
function loading_close() {
    $("#loading").hide();
}

function setItems(_el, _items, _selected = false) {
    if ($(_el).length) {
        var _label, _option;
        $(_items).each(function (index, row) {
            if (
                typeof row.subs != "undefined" &&
                Array.isArray(row.subs) &&
                row.subs.length
            ) {
                _label = $("<optgroup/>").attr("label", row.name);
                $(row.subs).each(function (i, r) {
                    if (
                        typeof r.subs != "undefined" &&
                        Array.isArray(r.subs) &&
                        r.subs.length
                    ) {
                        _option = $("<option/>")
                            .attr("value", r.id)
                            .attr("disabled", 1)
                            .text(r.name);
                        _label.append(_option);
                        $(r.subs).each(function (is, rs) {
                            _option = $("<option/>")
                                .attr("value", rs.id)
                                .html("&nbsp;&nbsp;&nbsp;" + rs.name);
                            _label.append(_option);
                        });
                    } else {
                        _option = $("<option/>").attr("value", r.id).text(r.name);
                        _label.append(_option);
                    }
                });
                $(_el).append(_label);
            } else {
                _option = $("<option/>").attr("value", row.id);
                _option.text(row.name);
                $(_el).append(_option);
            }
        });
        if (_selected) {
            $(_el).val(_selected).trigger("change");
        }
    }
}