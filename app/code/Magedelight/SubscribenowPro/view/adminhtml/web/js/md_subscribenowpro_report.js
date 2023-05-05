define(['jquery', 'mage/translate', 'md_moment_js', 'md_daterangepicker_js'], function ($, $t, moment, daterangepicker) {
    $.widget('md.subscribenowpro_report', {
        options: {
            version: '43',
            chartContainerElement: '#chart_container',
            defaultPeriod: 'last_7_days',
            datepickerElement: '#reportrange',
            reportTypeElement: '#reportType',
            reportGroupElement: '#reportGroup',
            chartTypeElement: '#chartType',
            chartImageElement: 'chart_image',
            isChartToTable: false,
            tableUiElement: '#chart_table_ui',
            noDataAvailableElement: '#no_data_available',
            printDivElement: 'print_div',
            store_id: null,
            url: null,
            from: null,
            to: null,
            data: null,
            isChartMultiColor: true,
            defaultChartColor: false,
            colors: [
                '#3366CC',
                '#DC3912',
                '#FF9900',
                '#109618',
                '#990099',
                '#3B3EAC',
                '#0099C6',
                '#DD4477',
                '#66AA00',
                '#B82E2E',
                '#316395',
                '#994499',
                '#22AA99',
                '#AAAA11',
                '#6633CC',
                '#E67300',
                '#8B0707',
                '#329262',
                '#5574A6',
                '#3B3EAC'
            ]
        },

        _create: function () {
            this.initializeTabs();
            this.initializeDateRangePicker();
            this.initializeDateRangePickerEvents();
            this.initializeReportTypeChangeEvent();
            this.initializeReportGroupChangeEvent();
            this.initializeChartTypeChangeEvent();
        },

        initializeTabs: function () {
            var self = this;

            $(document).on('click', '#diagram_tab ul[role=tablist] a', function (e) {
                $(this).parents('ul').find('li').removeClass('ui-tabs-active ui-state-active');
                $(this).parents('li').addClass('ui-tabs-active ui-state-active');
                $(self.options.reportTypeElement).val($(this).attr('data-graph')).trigger('change');
            });

            $(document).on('click', '.print_chart', function () {
                var printContent = document.getElementById(self.options.printDivElement);
                var winPrint = window.open('', '', 'left=0,top=0,width=800,height=900,toolbar=0,scrollbars=0,status=0');
                winPrint.document.write(printContent.innerHTML);
                winPrint.document.close();
                winPrint.focus();
                winPrint.print();
                winPrint.close();
            });
        },

        getRanges: function () {
            var self = this;
            //'Today': [moment(), moment()],
            //'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            return [
                {
                    key: 'last_7_days',
                    label: 'Last 7 Days',
                    from: moment().subtract(6, 'days'),
                    to: moment()
            },
                {
                    key: 'last_30_days',
                    label: 'Last 30 Days',
                    from: moment().subtract(29, 'days'),
                    to: moment()
            },
                {
                    key: 'current_month',
                    label: 'Current Month',
                    from: moment().startOf('month'),
                    //to: moment().endOf('month')
                    to: moment()
            },
                {
                    key: 'last_month',
                    label: 'Last Month',
                    from: moment().subtract(1, 'month').startOf('month'),
                    to: moment().subtract(1, 'month').endOf('month')
            },
                {
                    key: 'current_quarter',
                    label: 'Current Quarter',
                    from: self.getCurrentQuarterRange('from'),
                    to: self.getCurrentQuarterRange('to')
            },
                {
                    key: 'last_quarter',
                    label: 'Last Quarter',
                    from: self.getLastQuarterRange('from'),
                    to: self.getLastQuarterRange('to')
            },
                {
                    key: 'current_year',
                    label: 'Current Year',
                    from: moment().startOf('year'),
                    to: moment()
            },
                {
                    key: 'last_year',
                    label: 'Last Year',
                    from: moment().subtract(1, 'year').startOf('year').startOf('month'),
                    to: moment().subtract(1, 'year').endOf('year').endOf('month')
            }
            ];
        },

        getCurrentQuarterRange: function (type) {
            var current_month = parseInt(moment().format("M"));
            var quarter = Math.ceil(current_month / 3);

            if (type == 'from') {
                return moment().startOf('year').add((((quarter - 1) * 3)), 'month').startOf('month');
            } else if (type == 'to') {
                return moment().startOf('year').add(((quarter * 3) - 1), 'month').endOf('month');
            }
        },

        getLastQuarterRange: function (type) {
            var current_month = parseInt(moment().format("M"));
            var quarter = Math.ceil(current_month / 3);
            
            quarter = quarter - 1;

            if (quarter == 0) {
                quarter = 4;
                if (type == 'from') {
                    return moment().subtract(1, 'year').startOf('year').add((((quarter - 1) * 3)), 'month').startOf('month');
                } else if (type == 'to') {
                    return moment().subtract(1, 'year').startOf('year').add(((quarter * 3) - 1), 'month').endOf('month');
                }
            } else {
                if (type == 'from') {
                    return moment().startOf('year').add((((quarter - 1) * 3)), 'month').startOf('month');
                } else if (type == 'to') {
                    return moment().startOf('year').add(((quarter * 3) - 1), 'month').endOf('month');
                }
            }
        },

        initializeDateRangePicker: function () {
            var self = this;

            var ranges = self.getRanges();
            $.each(ranges, function (k, v) {
                if (v.key == self.options.defaultPeriod) {
                    $(self.options.datepickerElement).daterangepicker({
                        startDate: v.from,
                        endDate: v.to,
                        ranges: self.prepareDatepickerRanges(),
                        showDropdowns: true
                    }, function (from, to, label) {
                        self.chooseRange(from, to, label);
                    });

                    self.chooseRange(v.from, v.to, v.label);
                }
            });
        },

        initializeDateRangePickerEvents: function () {
            var self = this;
            $(self.options.datepickerElement).on('hide.daterangepicker', function (ev, picker) {
                setTimeout(function () {
                    $(self.options.datepickerElement).show();
                }, 100);
            });

            $(self.options.datepickerElement).on('apply.daterangepicker', function (ev, picker) {
                setTimeout(function () {
                    $(self.options.datepickerElement).show();
                }, 100);
            });
        },

        initializeReportTypeChangeEvent: function () {
            var self = this;
            $(self.options.reportTypeElement).on('change', function (e) {
                self.getReportDataAjax();
            });
        },
        
        initializeReportGroupChangeEvent: function () {
            var self = this;
            $(self.options.reportGroupElement).on('change', function (e) {
                self.getReportDataAjax();
            });
        },

        initializeChartTypeChangeEvent: function () {
            var self = this;
            $(self.options.chartTypeElement).on('change', function (e) {
                self.renderChart();
            });
        },

        prepareDatepickerRanges: function () {
            var rangeObject = {};
            var ranges = this.getRanges();
            $.each(ranges, function (k, v) {
                rangeObject[v.label] = [v.from, v.to];
            });

            return rangeObject;
        },

        chooseRange: function (from, to, label) {
            var self = this;
            self.options.from = from;
            self.options.to = to;
            
            if (label.toLowerCase() == 'custom range') {
                $(self.options.datepickerElement).find('span').html(from.format('MMMM D, YYYY') + ' - ' + to.format('MMMM D, YYYY'));
                $(self.options.reportGroupElement).parents('.dashboard-diagram-switcher').removeClass('mdssp-dashboard-diagram-switcher-hidden');
            } else {
                $(self.options.datepickerElement).find('span').html(label);
                $(self.options.reportGroupElement).parents('.dashboard-diagram-switcher').addClass('mdssp-dashboard-diagram-switcher-hidden');
            }

            self.setGroupBasedOnDateRange();
            self.getReportDataAjax();
        },

        setGroupBasedOnDateRange: function () {
            var diff = ((this.options.to.valueOf() - this.options.from.valueOf()) / 1000);
            diff = diff / (3600 * 24);
            
            if (diff > 365) {
                $(this.options.reportGroupElement).val('year');
            } else if (diff > 31) {
                $(this.options.reportGroupElement).val('month');
            } else {
                $(this.options.reportGroupElement).val('day');
            }
        },

        getReportDataAjax: function () {
            var self = this;

            var report_type = $(self.options.reportTypeElement).val();
            var group = $(self.options.reportGroupElement).val();

            $.ajax({
                url: self.options.url,
                showLoader: true,
                data: {
                    store_id: self.options.store_id,
                    report_type: report_type,
                    from: self.options.from.format('YYYY-MM-DD'),
                    to: self.options.to.format('YYYY-MM-DD'),
                    group: group
                },
                success: function (response) {
                    self.options.data = response.reportData;
                    self.renderChart();
                    self.renderTableUI(self.options.data);
                },
                error: function (error) {
                    console.log('error');
                }
            });
        },

        renderChart: function () {
            var self = this;
            var callbackFunction = $(self.options.chartTypeElement).val();

            if (self.options.data.length) {
                $(this.options.noDataAvailableElement).hide();
                $(this.options.chartContainerElement).show();

                self[callbackFunction](self.options.data);
            } else {
                self.renderNoDataAvailable();
            }
        },

        renderTableUI: function (data) {
            var self = this;

            if (!self.options.isChartToTable) {
                //$(self.options.tableUiElement).hide();
                //return false;
            }

            var html = '';
            if (data.length) {
                $.each(data, function (k, v) {
                    html += '<tr style="border-bottom: 1px solid">';
                    html += '<td style="padding: 5px 10px">'+v.x+'</td>';
                    html += '<td style="padding: 5px 10px">'+v.y+'</td>';
                    html += '</tr>';
                });
            }

            $(self.options.tableUiElement+' tbody').html(html);
        },

        renderNoDataAvailable: function () {
            $(this.options.noDataAvailableElement).show();
            $(this.options.chartContainerElement).hide();
        },

        getReportTitle: function () {
            var title = false;
            var report_type = $(this.options.reportTypeElement).val();
            switch (report_type) {
                case 'subscription':
                default:
                    title = $t('Subscription Report');
                    break;

                case 'order':
                    title = $t('Order Report');
                    break;

                case 'amount':
                    title = $t('Amount Report');
                    break;
            }

            return title;
        },

        getXAxisTitle: function () {
            return $t('Period');
        },

        getYAxisTitle: function () {
            var title = false;
            var report_type = $(this.options.reportTypeElement).val();
            switch (report_type) {
                case 'subscription':
                default:
                    title = $t('Subscription Summary');
                    break;

                case 'order':
                    title = $t('Order Summary');
                    break;

                case 'amount':
                    title = $t('Amount Summary');
                    break;
            }

            return title;
        },

        getYAxisPointTitle: function () {
            var title = false;
            var report_type = $(this.options.reportTypeElement).val();
            switch (report_type) {
                case 'subscription':
                default:
                    title = $t('Subscriptions');
                    break;

                case 'order':
                    title = $t('Orders');
                    break;

                case 'amount':
                    title = $t('Amount');
                    break;
            }

            return title;
        },

        getDefaultChartColor: function () {
            if (!this.options.defaultChartColor) {
                return '#3366cc';
            }

            return this.options.defaultChartColor;
        },

        getRandomColor: function () {
            if (!this.options.isChartMultiColor) {
                return this.getDefaultChartColor();
            }

            return this.options.colors[Math.floor(Math.random()*this.options.colors.length)];
        },

        preparePrintChartElement: function (chart) {
            var chart_image_div = document.getElementById(this.options.chartImageElement);
            chart_image_div.innerHTML = '<img src="' + chart.getImageURI() + '">';
        },

        printChart: function () {
            var printContent = document.getElementById(this.options.chartImageElement);
            var winPrint = window.open('', '', 'left=0,top=0,width=800,height=900,toolbar=0,scrollbars=0,status=0');
            winPrint.document.write(printContent.innerHTML);
            winPrint.document.close();
            winPrint.focus();
            winPrint.print();
            winPrint.close();
        },

        renderGoogleLineChart: function (data) {
            var self = this;
            self.options.data = data;

            google.charts.load(self.options.version, {'packages':['corechart']});
            google.charts.setOnLoadCallback(drawGoogleLineChartChart);

            function drawGoogleLineChartChart()
            {
                var arrayToDataTable = [];
                arrayToDataTable.push(['Period', self.getYAxisPointTitle(), {role: 'style'}]);
                
                $.each(self.options.data, function (k, v) {
                    arrayToDataTable.push([v.x, v.y, self.getRandomColor()]);
                });
                
                var data = google.visualization.arrayToDataTable(
                    arrayToDataTable
                );

                var options = {
                    title: self.getReportTitle(),
                    vAxis: {
                        title: self.getYAxisTitle()
                    },
                    hAxis: {
                        title: self.getXAxisTitle()
                    },
                    curveType: 'none', // none|function
                    legend: { position: 'none' }, // none|bottom|top|right|left
                    animation: {
                        duration: 1000,
                        easing: 'out',
                        startup: true
                    }
                };

                var chart = new google.visualization.LineChart(document.getElementById('chart'));
                chart.draw(data, options);

                self.preparePrintChartElement(chart);
            }
        },

        renderGoogleColumnChart: function (data) {
            var self = this;
            self.options.data = data;

            google.charts.load(self.options.version, {'packages':['corechart']});
            google.charts.setOnLoadCallback(drawGoogleColumnChartChart);

            function drawGoogleColumnChartChart()
            {
                var arrayToDataTable = [];
                arrayToDataTable.push(['Period', self.getYAxisPointTitle(), {role: 'style'}]);

                $.each(self.options.data, function (k, v) {
                    arrayToDataTable.push([v.x, parseNumber(v.y), self.getRandomColor()]);
                });

                /* arrayToDataTable = [
                    ["Period","Subscriptions",{"role":"style"}],
                    ["May-01",'0',"#3366cc"],
                    ["May-02",'0',"#3366cc"],
                    ["May-03",'0',"#3366cc"],
                    ["May-04",'0',"#3366cc"],
                    ["May-05",'0',"#3366cc"],
                    ["May-06",'0',"#3366cc"],
                    ["May-07",'1',"#3366cc"]
                ]; */

                var data = google.visualization.arrayToDataTable(
                    arrayToDataTable
                );

                var options = {
                    title: self.getReportTitle(),
                    vAxis: {
                        title: self.getYAxisTitle()
                    },
                    hAxis: {
                        title: self.getXAxisTitle()
                    },
                    curveType: 'none', // none|function
                    legend: { position: 'none' }, // none|bottom|top|right|left
                    animation: {
                        duration: 1000,
                        easing: 'out',
                        startup: true
                    }
                };

                var chart = new google.visualization.ColumnChart(document.getElementById('chart'));
                chart.draw(data, options);

                self.preparePrintChartElement(chart);
            }
        },

        renderGoogleBarChart: function (data) {
            var self = this;
            self.options.data = data;

            google.charts.load(self.options.version, {'packages':['corechart']});
            google.charts.setOnLoadCallback(drawGoogleBarChartChart);

            function drawGoogleBarChartChart()
            {
                var arrayToDataTable = [];
                arrayToDataTable.push(['Period', self.getYAxisPointTitle(), {role: 'style'}]);

                $.each(self.options.data, function (k, v) {
                    arrayToDataTable.push([v.x, v.y, self.getRandomColor()]);
                });

                var data = google.visualization.arrayToDataTable(
                    arrayToDataTable
                );

                var options = {
                    title: self.getReportTitle(),
                    vAxis: {
                        title: self.getYAxisTitle()
                    },
                    hAxis: {
                        title: self.getXAxisTitle()
                    },
                    curveType: 'none', // none|function
                    legend: { position: 'none' }, // none|bottom|top|right|left
                    animation: {
                        duration: 1000,
                        easing: 'out',
                        startup: true
                    }
                };

                var chart = new google.visualization.BarChart(document.getElementById('chart'));
                chart.draw(data, options);

                self.preparePrintChartElement(chart);
            }
        }
    });

    return $.md.subscribenowpro_report;
});