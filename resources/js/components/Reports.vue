<template>
    <div id="ReportsPage" class="page-content-detail">
        <div class="page-content-block-wrapper">
            <div class="page-header">
                <span>Reports</span>
                <p class="header-summary">Show analytics and traffic on the site.</p>
            </div>
        </div>

        <div class="page-content-block-wrapper">
            <div class="car-content row">
                <div class="col-md-3 col-sm-12" v-show="loading">
                    <div class="white-card">
                        <!-- <div class="card-more"><span class="mif-more-vert"></span></div> -->
                        <div class="card-header">
                            <div>Vehicles Purchased</div>
                            <div class="bar_chart">
                                <apexchart :options="vehicleChartOption" :series="vehicleSeries"></apexchart>
                            </div>

                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6 body-left">THIS MONTH</div>
                                <div class="col-6 body-right">{{ calculate("thisMonth", "purchased") }} vehicles</div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row padding-bottom-5">
                                <div class="col-6">Last Month</div>
                                <div class="col-6">This Year</div>
                            </div>
                            <div class="row">
                                <div class="col-6 border-right">
                                    <div class="left-content">{{ calculate("lastMonth", "purchased") }}</div>
                                    <div class="space-content">Month After</div>
                                    <div class="left-content">{{ calculate("last3Month", "purchased") }}</div>
                                </div>
                                <div class="col-6">
                                    <div class="right-content">{{ calculate("thisYear", "purchased") }}</div>
                                    <div class="space-content">Last Year</div>
                                    <div class="right-content">{{ calculate("lastYear", "purchased") }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-12" v-show="loading">
                    <div class="white-card">
                        <!-- <div class="card-more"><span class="mif-more-vert"></span></div> -->
                        <div class="card-header">
                            <div>Closing Rate</div>
                            <div class="bar_chart">
                                <apexchart :options="closingChartOption" :series="closingSeries"></apexchart>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6 body-left">THIS MONTH</div>
                                <div class="col-6 body-right">{{ calculate("thisMonth", "closing_rate") }}%</div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row padding-bottom-5">
                                <div class="col-6">Last Month</div>
                                <div class="col-6">This Year</div>
                            </div>
                            <div class="row">
                                <div class="col-6 border-right">
                                    <div class="left-content">{{ calculate("lastMonth", "closing_rate") }}%</div>
                                    <div class="space-content">Month After</div>
                                    <div class="left-content">{{ calculate("last3Month", "closing_rate") }}%</div>
                                </div>
                                <div class="col-6">
                                    <div class="right-content">{{ calculate("thisYear", "closing_rate") }}%</div>
                                    <div class="space-content">Last Year</div>
                                    <div class="right-content">{{ calculate("lastYear", "closing_rate") }}%</div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="col-md-3 col-sm-12" v-show="loading">
                    <div class="white-card">
                        <!-- <div class="card-more"><span class="mif-more-vert"></span></div> -->
                        <div class="card-header">
                            Pickup Rate
                            <div class="bar_chart">
                                <apexchart :options="pickupChartOption" :series="pickupSeries"></apexchart>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6 body-left">THIS MONTH</div>
                                <div class="col-6 body-right">{{ calculate("thisMonth", "pickup_rate") }}%</div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row padding-bottom-5">
                                <div class="col-6">Last Month</div>
                                <div class="col-6">This Year</div>
                            </div>
                            <div class="row">
                                <div class="col-6 border-right">
                                    <div class="left-content">{{ calculate("lastMonth", "pickup_rate") }}%</div>
                                    <div class="space-content">Month After</div>
                                    <div class="left-content">{{ calculate("last3Month", "pickup_rate") }}%</div>
                                </div>
                                <div class="col-6">
                                    <div class="right-content">{{ calculate("thisYear", "pickup_rate") }}%</div>
                                    <div class="space-content">Last Year</div>
                                    <div class="right-content">{{ calculate("lastYear", "pickup_rate") }}%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- <div class="col-md-3 col-sm-12" v-show="loading">
                    <div class="white-card">
                        <div class="card-more"><span class="mif-more-vert"></span></div>
                        <div class="card-header">
                            Average Lifecycle
                            <h2 class="chart">{CHART PART}</h2>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6 body-left">THIS MONTH</div>
                                <div class="col-6 body-right">3 Days</div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row padding-bottom-5">
                                <div class="col-6">Last Month</div>
                                <div class="col-6">This Year</div>
                            </div>
                            <div class="row">
                                <div class="col-6 border-right">
                                    <div class="left-content">4 Days</div>
                                    <div class="space-content">Month After</div>
                                    <div class="left-content">8 Days</div>
                                </div>
                                <div class="col-6">
                                    <div class="right-content">6 Days</div>
                                    <div class="space-content">Last Year</div>
                                    <div class="right-content">7 Days</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->

                <div class="col-md-3 col-sm-12" v-show="loading">
                    <div class="white-card">
                        <h2 style="margin-top: 45%;">Coming Soon..</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="page-content-block-wrapper padding-0">
            <div class="row footer-wrapper">

            </div>
        </div>

    </div>
</template>

<script>
import EventBus from '../event-bus';
import CommonService from '../services/CommonService';
var commonService = new CommonService();

    export default {
        data() {
            return {
                loading: false,
                thisMonth: null,
                lastMonth: null,
                last3Month: null,
                thisYear: null,
                lastYear: null,
                thisMonthIndices: [],
                lastMonthIndices: [],
                last3MonthIndices: [],
                thisYearIndices: [],
                lastYearIndices: [],
                chartWidth: "100%",
                chartHeigth: "50%",
                vehicleChartOption: {
                    chart: {
                        id: 'vehicleChart',
                        height: '50%',
                        type: 'bar',
                        width: '100%',
                    },
                    plotOptions: {
                        bar: {
                            dataLabels: {
                                position: 'top', // top, center, bottom
                            },
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function (val) {
                            return val;
                        },
                        offsetY: -20,
                        style: {
                            fontSize: '12px',
                            colors: ["#304758"]
                        }
                    },
                    xaxis: {
                        categories: ["Month Before", "Last Month", "This Month"]
                    }
                },
                closingChartOption: {
                    chart: {
                        id: 'closingChart',
                        height: '50%',
                        type: 'bar',
                        width: '100%',
                    },
                    plotOptions: {
                        bar: {
                            dataLabels: {
                                position: 'top', // top, center, bottom
                            },
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function (val) {
                            return val + "%";
                        },
                        offsetY: -20,
                        style: {
                            fontSize: '12px',
                            colors: ["#304758"]
                        }
                    },
                    xaxis: {
                        categories: ["Month Before", "Last Month", "This Month"]
                    },
                    yaxis: {
                        max: 100
                    }
                },
                pickupChartOption: {
                    chart: {
                        id: 'pickupChart',
                        height: '50%',
                        type: 'bar',
                        width: '100%',
                    },
                    plotOptions: {
                        bar: {
                            dataLabels: {
                                position: 'top', // top, center, bottom
                            },
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function (val) {
                            return val + "%";
                        },
                        offsetY: -20,
                        style: {
                            fontSize: '12px',
                            colors: ["#304758"]
                        }
                    },
                    xaxis: {
                        categories: ["Month Before", "Last Month", "This Month"]
                    },
                    yaxis: {
                        max: 100
                    }
                },
                vehicleSeries: [],
                closingSeries: [],
                pickupSeries: [],
            }
        },
        created() {

        },
        beforeDestroy () {

        },
        mounted() {
            this.refreshPage();
        },
        filters: {

        },
        methods: {
            refreshPage() {
                let url = '/api/report';
                let loader = this.$loading.show();
                this.axios
                .get(url, commonService.get_api_header())
                .then(response => {
                    loader.hide();
                    var res = response.data;
                    this.parseJSONData(res, ['thisMonth', 'lastMonth', 'last3Month', 'thisYear', 'lastYear'], res.accountName);
                    this.loading = true;
                    var purchased3MonthBefore = this.calculate("last3Month", "purchased") - this.calculate("thisMonth", "purchased") - this.calculate("lastMonth", "purchased");

                    this.vehicleSeries= [{
                        name: 'vehicle',
                        data: [purchased3MonthBefore, this.calculate("lastMonth", "purchased"), this.calculate("thisMonth", "purchased") ]
                    }];

                    var paid3MonthBefore = this.getItemValue("last3Month", 1) - this.getItemValue("lastMonth", 1) - this.getItemValue("thisMonth", 1);
                    var pickup3MonthBefore = this.getItemValue("last3Month", 2) - this.getItemValue("lastMonth", 2) - this.getItemValue("thisMonth", 2);
                    this.closingSeries= [{
                        name: 'closing',
                        data: [ parseInt(100 * paid3MonthBefore/purchased3MonthBefore) || 0, this.calculate("lastMonth", "closing_rate"), this.calculate("thisMonth", "closing_rate") ]
                    }];
                    this.pickupSeries= [{
                        name: 'pickup',
                        data: [ parseInt(100 * pickup3MonthBefore/purchased3MonthBefore) || 0, this.calculate("lastMonth", "pickup_rate"), this.calculate("thisMonth", "pickup_rate") ]
                    }];
                }).catch((error) => {
                    loader.hide();
                    var status = error.response.status;
                    if (status == 401) {
                        commonService.logout();
                        this.$router.push('login');
                    } else {
                        alert('Api request error');
                    }
                });
            },
            parseJSONData(res_data, keys, target) {
                var report;
                for(let i=0; i<keys.length; i++) {
                    report = JSON.stringify(res_data[keys[i]]);
                    report = report.replace(/\\t/g, "").replace(/\\n/g, "");
                    report = JSON.parse(JSON.parse(report).replace(/(?:\\[rn])+/g, '').replace(/\\'/g, ""));
                    if(report.response) {
                        var result = report.response.result;
                        var filtered_arr = result.rows.filter(row=> {
                            return row[0] == target
                        });
                        this[keys[i]] = filtered_arr.length? filtered_arr[0] : new Array(20).fill(0);
                        var cancelledIndex = result.column_order.indexOf("Cancelled");
                        var pickedupIndex = result.column_order.indexOf("Picked Up");
                        var paidIndex = result.column_order.indexOf("Paid");
                        var arr = [cancelledIndex, pickedupIndex, paidIndex];
                        this[keys[i] + "Indices"] = arr;
                    }

                }
            },
            calculate(item, card) {
                var cancelled = this.getItemValue(item, 0);
                var pickedup = this.getItemValue(item, 1);
                var paid = this.getItemValue(item, 2);
                if(card == "purchased") {
                    return cancelled + pickedup + paid;
                }
                if(card == "closing_rate") {
                    return (cancelled + pickedup + paid)>0? parseInt(paid * 100 /(cancelled + pickedup + paid)) : 0;
                }
                if(card == "pickup_rate") {
                    return (cancelled + pickedup + paid)>0? parseInt(pickedup * 100 /(cancelled + pickedup + paid)) : 0;
                }
            },
            getItemValue(item, index) {
                return this[item + "Indices"][index]>=0? parseInt(this[item][this[item + "Indices"][index]] || 0) : 0;
            }
        }
    }
</script>
