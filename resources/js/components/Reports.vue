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
                <div class="col-md-3 col-sm-12 padding-left-0">
                    <div class="white-card">
                        <div class="card-more"><span class="mif-more-vert"></span></div>
                        <div class="card-header">
                            Vehicles Purchased
                            <h2 class="chart">{CHART PART}</h2>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6 body-left">THIS MONTH</div>
                                <div class="col-6 body-right">8 vehicles</div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row padding-bottom-5">
                                <div class="col-6">Last Month</div>
                                <div class="col-6">This Year</div>
                            </div>
                            <div class="row">
                                <div class="col-6 border-right">
                                    <div class="left-content">18</div>
                                    <div class="space-content">Month After</div>
                                    <div class="left-content">17</div>
                                </div>
                                <div class="col-6">
                                    <div class="right-content">23</div>
                                    <div class="space-content">Last Year</div>
                                    <div class="right-content">87</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-12">
                    <div class="white-card">
                        <div class="card-more"><span class="mif-more-vert"></span></div>
                        <div class="card-header">
                            Closing Rate
                            <h2 class="chart">{CHART PART}</h2>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6 body-left">THIS MONTH</div>
                                <div class="col-6 body-right">75%</div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row padding-bottom-5">
                                <div class="col-6">Last Month</div>
                                <div class="col-6">This Year</div>
                            </div>
                            <div class="row">
                                <div class="col-6 border-right">
                                    <div class="left-content">87%</div>
                                    <div class="space-content">Month After</div>
                                    <div class="left-content">89%</div>
                                </div>
                                <div class="col-6">
                                    <div class="right-content">90%</div>
                                    <div class="space-content">Last Year</div>
                                    <div class="right-content">90%</div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="col-md-3 col-sm-12">
                    <div class="white-card">
                        <div class="card-more"><span class="mif-more-vert"></span></div>
                        <div class="card-header">
                            Pickup Rate
                            <h2 class="chart">{CHART PART}</h2>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6 body-left">THIS MONTH</div>
                                <div class="col-6 body-right">75%</div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row padding-bottom-5">
                                <div class="col-6">Last Month</div>
                                <div class="col-6">This Year</div>
                            </div>
                            <div class="row">
                                <div class="col-6 border-right">
                                    <div class="left-content">87%</div>
                                    <div class="space-content">Month After</div>
                                    <div class="left-content">89%</div>
                                </div>
                                <div class="col-6">
                                    <div class="right-content">90%</div>
                                    <div class="space-content">Last Year</div>
                                    <div class="right-content">90%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-12 padding-right-0">
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

            }
        },
        created() {

        },
        beforeDestroy () {

        },
        mounted() {

        },
        filters: {

        },
        methods: {

        }
    }
</script>
