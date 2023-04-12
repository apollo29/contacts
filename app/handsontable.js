$(document).ready(function () {
    const container = document.querySelector('#example')
    const searchField = document.querySelector('#search_field');
    const hot = new Handsontable(container, {
        data: data_handsontable,
        cells: function (row, col, prop) {
            let cellProperties = {};
            cellProperties.data = col;
            if (columns_handsontable[col]!==undefined && columns_handsontable[col]['type']!==undefined && columns_handsontable[col]['type']==="checkbox") {
                cellProperties.type = columns_handsontable[col]['type'];
                cellProperties.checkedTemplate = "x"
                cellProperties.uncheckedTemplate = "";
            }
            return cellProperties;
        },
        contextMenu: {
            callback(key, selection, clickEvent) {
                // Common callback for all options
                if (key==="col_left") {
                    console.log(selection[0]['start']['col'],this.getData()[selection[0]['start']['col']]);
                    columns_handsontable.splice(selection[0]['start']['col'], 0, {data:selection[0]['start']['col']});
                }
            },
            items: {
                row_above: {
                    disabled() {
                        // Disable option when first row was clicked
                        return this.getSelectedLast()[0] === 0;
                    }
                },
                row_below: {

                },
                sp1: '---------',
                col_left: {

                },
                col_right: {

                },
                sp2: '---------',
                remove_row: {

                }
            }
        },
        rowHeaders: true,
        colHeaders: headers,
        multiColumnSorting: true,
        dropdownMenu: true,
        filters: true,
        search: true,
        language: 'de-CH',
        licenseKey: 'non-commercial-and-evaluation',
    });

    // add a search input listener
    searchField.addEventListener('keyup', function (event) {
        const search = hot.getPlugin('search');
        search.query(event.target.value);
        hot.render();
    });

    const exportPlugin = hot.getPlugin('exportFile');
    $('.export_csv').click(function () {
        exportPlugin.downloadFile('csv', {filename: 'Adressdatenbank'});
    });

    $('.store_csv').click(function () {
        // clear filters to get all data
        hot.getPlugin('Filters').clearConditions();
        hot.getPlugin('Filters').filter();
        hot.render();

        let csv_string = exportPlugin.exportAsString('csv', {
            exportHiddenRows: true,
            exportHiddenColumns: true,
            columnHeaders: true,
            columnDelimiter: ';'
        });

        let b64 = Base64.encode(csv_string);

        $('form[name=addresses_form] input[name="addresses_form_data"]').val(b64);
        $('form[name=addresses_form]').submit();
    });
});