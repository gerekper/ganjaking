(function ($) {

    $(window).on('elementor/frontend/init', function () {

        var PremiumTableHandler = function ($scope, $) {

            var $tableElem = $scope.find(".premium-table"),
                $premiumTableWrap = $scope.find(".premium-table-wrap"),
                settings = $tableElem.data("settings");

            if (!settings)
                return;

            //Table Sort
            if (settings.sort) {
                if (
                    $(window).outerWidth() > 767 ||
                    ($(window).outerWidth() < 767 && settings.sortMob)
                ) {
                    $tableElem.tablesorter({
                        cssHeader: "premium-table-sort-head",
                        cssAsc: "premium-table-up",
                        cssDesc: "premium-table-down",
                        usNumberFormat: settings.usNumbers,
                        sortReset: true,
                        sortRestart: true
                    });
                } else {
                    $tableElem.find(".premium-table-sort-icon").css("display", "none");
                }
            }

            //Table search
            if (settings.search) {
                $premiumTableWrap.find(".premium-table-search-field").keyup(function () {

                    _this = this;

                    $tableElem.find("tbody tr").each(function () {

                        if ($(this).text().toLowerCase().indexOf($(_this).val().toLowerCase()) === -1) {
                            $(this).addClass("premium-table-search-hide");
                        } else {

                            $(this).removeClass("premium-table-search-hide");
                            if ($(this).hasClass('premium-table-row-hidden')) {
                                $(this).removeClass("premium-table-row-hidden").addClass('hidden-by-default');
                            }

                        }

                    });

                    if ($(_this).val().toLowerCase().length === 0) {
                        $tableElem.find(".hidden-by-default").each(function () {
                            $(this).addClass('premium-table-row-hidden').removeClass('hidden-by-default');
                        });
                    }
                });

            }

            //Table show records
            if (settings.records) {

                $premiumTableWrap
                    .find(".premium-table-records-box")
                    .on("change", function () {
                        var rows = $(this)
                            .find("option:last")
                            .val(),
                            value = parseInt(this.value);

                        if (1 === value) {
                            $tableElem.find("tbody tr").not(".premium-table-search-hide").removeClass("premium-table-hide");
                        } else {
                            $tableElem.find("tbody tr:gt(" + (value - 2) + ")").not(".premium-table-search-hide").addClass("premium-table-hide");

                            $tableElem.find("tbody tr:lt(" + (value - 1) + ")").not(".premium-table-search-hide").removeClass("premium-table-hide");
                        }
                    });
            }

            //Tables with CSV Files
            if (settings.dataType === "csv" && '' != settings.csvFile) {

                //If the file is Google Spreadsheet, then we need to  use CORS Proxy.
                // if (-1 !== settings.csvFile.indexOf("docs.google.com"))
                //     settings.csvFile = "https://cors-anywhere.herokuapp.com/" + settings.csvFile;

                $.ajax({
                    url: PremiumProSettings.ajaxurl,
                    type: "POST",
                    data: {
                        action: 'handle_table_data',
                        id: settings.id,
                        security: PremiumProSettings.nonce,
                    },
                    success: function (res) {

                        if (res.data) {

                            handleCsvData(res.data);

                            if (settings.pagination === "yes")
                                handleTablePagination();

                        } else {

                            $.ajax({
                                url: settings.csvFile,
                                type: "GET",
                                success: function (res) {

                                    $.ajax({
                                        url: PremiumProSettings.ajaxurl,
                                        type: "POST",
                                        data: {
                                            action: 'handle_table_data',
                                            id: settings.id,
                                            expire: settings.reload,
                                            tableData: res,
                                            security: PremiumProSettings.nonce,
                                        },
                                        success: function (res) {
                                            console.log(res);
                                        }
                                    });

                                    if (!res)
                                        return;

                                    handleCsvData(res);

                                    if (settings.pagination === "yes")
                                        handleTablePagination();

                                },
                                error: function (err) {
                                    console.log(err);
                                }
                            });

                        }

                    }
                });

                //Handle CSV Data
                function handleCsvData(data) {

                    var rowsData = data.split(/\r?\n|\r/),
                        firstRow = settings.firstRow,
                        table_data = "head" === firstRow ? '<thead class="premium-table-head">' : '<tbody class="premium-table-body">';

                    for (var count = 0; count < rowsData.length; count++) {
                        var cell_data = rowsData[count].split(settings.separator);
                        table_data += '<tr class="premium-table-row">';
                        for (
                            var cell_count = 0; cell_count < cell_data.length; cell_count++
                        ) {
                            if (count === 0 && "head" === firstRow) {
                                table_data +=
                                    '<th class="premium-table-cell"><span class="premium-table-text">' +
                                    cell_data[cell_count];
                                table_data += "</span></th>";
                            } else {
                                table_data +=
                                    '<td class="premium-table-cell"><span class="premium-table-text">' +
                                    cell_data[cell_count] +
                                    "</span></td>";
                            }
                        }
                        table_data += "</tr>";
                        if (count === 0 && "head" === firstRow) {
                            table_data += "</thead>";
                        }
                    }
                    $tableElem.html("");
                    $tableElem.html(table_data);
                }

            }


            if (settings.dataType === "custom" && settings.pagination === "yes")
                handleTablePagination();

            function handleTablePagination() {

                var tableRows = $tableElem.find("tbody tr").length,
                    pages = Math.ceil(tableRows / settings.rows);

                $tableElem.find("tbody tr:gt(" + (settings.rows - 1) + ")").addClass("premium-table-row-hidden");

                var paginationHtml = '';
                for (var count = 0; count < pages; count++) {
                    var current = 0 === count ? "current" : "";
                    paginationHtml += "<li><a href='#' class='page-numbers " + current + "' data-page='" + count + "'>" + (count + 1) + "</a></li>";
                }

                $scope.find(".premium-table-pagination li").eq(0).after(paginationHtml);


                $scope.on("click", ".premium-table-pagination li a", function (e) {
                    e.preventDefault();

                    var $this = $(this);

                    if ($this.hasClass("current") || $this.hasClass("custom-page"))
                        return;

                    $premiumTableWrap.append('<div class="premium-loading-feed"><div class="premium-loader"></div></div>');

                    setTimeout(function () {

                        var page = $this.data("page");

                        $tableElem.find("tbody tr").removeClass("premium-table-row-hidden");

                        $scope.find(".premium-table-pagination a.current").removeClass("current");

                        if (!$this.hasClass("prev") && !$this.hasClass("next"))
                            $this.addClass("current");

                        if ($this.hasClass('next') || (pages - 1) === page) {
                            $tableElem.find("tbody tr:lt(" + ((pages - 1) * settings.rows) + ")").addClass("premium-table-row-hidden");
                        } else if ($this.hasClass('prev') || 0 === page) {
                            $tableElem.find("tbody tr:gt(" + (settings.rows - 1) + ")").addClass("premium-table-row-hidden");
                        } else {
                            var gt = ((page + 1) * settings.rows - 1);
                            $tableElem.find("tbody tr:gt(" + gt + ")").addClass("premium-table-row-hidden");
                            $tableElem.find("tbody tr:lt(" + page * settings.rows + ")").addClass("premium-table-row-hidden");
                        }

                        $premiumTableWrap.find(".premium-loading-feed").remove();
                        $('html, body').animate({
                            scrollTop: (($premiumTableWrap.offset().top) - 100)
                        }, 'slow');
                    }, 1000);

                });


            }

        };

        elementorFrontend.hooks.addAction('frontend/element_ready/premium-tables-addon.default', PremiumTableHandler);
    });
})(jQuery);