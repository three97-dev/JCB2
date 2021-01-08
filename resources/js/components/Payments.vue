<template>
    <div id="PaymentsPage" class="page-content-detail">
        <div class="page-header">
            <span>Your Payments</span>
        </div>
        <div class="page-filter">
            <span class="filter-label">Filters:</span>            
            <div class="filter-content" v-if="filter_string != ''">
                <a href="javascript:;" class="mif-cancel text-danger" v-on:click="resetFilter()"></a>
                {{filter_string}}
            </div>
        </div>

        <div class="mobile-total-header">
            {{total}} RESULTS
        </div>

        <div class="car-content">
            <div class="car-left-content col-md-8">
                <div class="title-header">
                    <div class="title">
                        <input type="checkbox" v-model="checked_all" v-on:change="checkAll(true)">&nbsp;Status
                    </div>
                    <div class="title">Year</div>
                    <div class="title">Make</div>
                    <div class="title">Model</div>
                    <div class="title text-center">Current Offer</div>
                    <div class="action-go"></div>
                </div>
                <div class="car-body">
                    <div class="car-item" v-for="car in cars" :key="car.index" v-bind:class="{'selected': sel_car && car.index == sel_car.index}">
                        <div class="item-data">
                            <input type="checkbox" style="margin-top: 4px;" v-model="car.is_checked" v-on:change="checkAll()">&nbsp;
                            <div v-if="car.Stage=='Paid'" class="status-active"> Paid </div>
                            <div v-if="car.Stage=='Deal Made'" class="status-won"> Unpaid </div>
                            <div v-if="car.Stage=='Picked Up'" class="status-fail"> Overdue </div>
                        </div>
                        <div class="item-data">{{ car.Year }}</div>
                        <div class="item-data">{{ car.Make }}</div>
                        <div class="item-data">{{ car.Model }}</div>
                        <div class="item-data text-center">{{ car.Buyers_Quote }}</div>
                        <a href="javascript:;" class="text-center action-go" v-on:click="showDetail(car)">
                            <span class="mif-arrow-right"></span>
                        </a>
                        <div class="mobile-item item-data"  v-on:click="showDetail(car)">
                            <div class="item-content">
                                <div class="font-weight-bold">{{car.Reference_Number}} &nbsp;&nbsp;{{car.Year}} {{car.Make}} {{car.Model}}</div>
                                <div>{{car.City}} &nbsp;&nbsp; {{car.Zip_Code}}</div>
                                <div style="display:flex;justify-content:space-between;">
                                    <div class="text-blue">{{car.Buyers_Quote}}</div>
                                    <div v-if="car.Stage=='Paid'" class="status-active"> Paid </div>
                                    <div v-if="car.Stage=='Deal Made'" class="status-won"> Unpaid </div>
                                    <div v-if="car.Stage=='Picked Up'" class="status-fail"> Overdue </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pagination">
                        <div class="page-label">
                            Showing <span> {{(page-1) * records_per_page + 1 }} </span> to <span> {{ (page-1) * records_per_page + cars.length }} </span> of {{total}} Available Cars
                        </div>
                        <div class="pages-action">
                            Page: 
                            <template v-for="one of valid_pages">
                                <a :key="one"  class="btn-page" v-bind:class="{active: one == page}" href="javascript:;" v-on:click="refreshPage(one)">{{one}}</a>
                            </template>
                            <a class="btn-page"  href="javascript:;" v-on:click="refreshPage(page-1)">&lt; Prev</a>
                            <a class="btn-page"  href="javascript:;" v-on:click="refreshPage(page+1)">Next &gt;</a>
                        </div>
                    </div>
                    
                </div>
            </div>
            <div class="car-right-content col-md-4" v-if="!is_mobile_view || (is_mobile_view && sel_car)">
                <a href="javascript:;"  v-if="sel_car && !submit_payment" class="text-danger btn-clear" v-on:click="sel_car=null">
                    <span class="mif-cancel"></span>
                    Clear
                </a>

                <a href="javascript:;" class="btn-close-car-detail" v-on:click="sel_car=null">
                    <span class="mif-cross-light"></span>
                </a>
                <div class="car-detail">
                    <div class="empty-content" v-if="!sel_car">
                        Click a list item to view details
                    </div>
                    <div class="submit-content" v-if="sel_car && submit_payment">
                        <div class="title font-weight-bold text-center">
                            <div class="">
                                YOUR PAYMENT OF <span class="text-blue">${{pay_info.amount}}</span>
                            </div>
                            <div class="text-center">
                                WAS SUBMITTED
                            </div>
                            
                        </div>
                        <div class="bid-image"><img src="/img/payment-success.png" alt=""></div>
                        <div class="action-bar text-right">
                            <button class="btn btn-primary" v-on:click="sel_car=null">DONE</button>
                        </div>
                    </div>
                    <div class="selcar-content" v-if="sel_car && !submit_payment">
                        <div class="title text-blue">Make Payment</div>
                        <div class="selcar-detail row">
                            <div class="col-md-12 field-item">
                                <div class="item-label">Card Number</div>
                                <the-mask class="item-value" :mask="'####-####-####-####'" placeholder="0000-0000-0000-0000" v-model="pay_info.card_no"/>
                            </div>
                            <div class="col-md-12 field-item">
                                <div class="item-label">Cardholder Name</div>
                                <input type="text" class="item-value" v-model="pay_info.card_name" placeholder="Your name">
                            </div>
                            <div class="col-md-6 field-item">
                                <div class="item-label">Card Expiry</div>
                                <the-mask class="item-value" :mask="'##/##'" placeholder="MM/YY" v-model="pay_info.exp"/>
                            </div>
                            <div class="col-md-6 field-item">
                                <div class="item-label">CVC Number</div>
                                <the-mask class="item-value" :mask="'###'" placeholder="XXX" v-model="pay_info.cvc"/>
                            </div>
                            <div class="col-md-12 field-item">
                                <div class="item-label">Amount</div>
                                <input type="number" class="item-value txt-amount" v-model="pay_info.amount" placeholder="">
                            </div>
                        </div>
                        <div class="action-bar">
                            <div>
                                <img src="/img/payment-card.png" alt="">
                            </div>
                            <button class="btn btn-primary" v-on:click="submitPayment()">SUBMIT PAYMENT</button>
                        </div>
                    </div>
                </div>
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
                cars: [],
                page: 1,
                records_per_page: 8,
                total: '-',
                valid_pages: [],
                filter_param: {},
                filter_like: false,
                filter_string: '',
                sel_car: null,
                bid_price: '',
                pay_info : {},
                checked_all: false,
                is_mobile_view: window.innerWidth <= 992,
            }
        },
        created() {
            const thiz = this;
            EventBus.$on('update-payments-filter', function(filter_param) {
                thiz.filter_param = filter_param;
                thiz.filter_string = thiz.filter_param['filter_string'];
                delete thiz.filter_param['filter_string'];
                thiz.refreshPage(1);
            });
        },
        beforeDestroy () {
            EventBus.$off('update-bid-filter');
        },
        mounted() {
            this.refreshPage(1);
        },
        methods: {
            checkAll(force = false) {
                if (force) {
                    this.cars.forEach(one => one.is_checked = this.checked_all)
                } else {
                    this.checked_all = this.cars.length == this.cars.filter(one => one.is_checked).length;
                }
            },
            refreshPage(page) {
                 if (!page) page = this.page;
                if (page < 1 || page > parseInt(this.total/this.records_per_page) + 1) return;
                this.page = page;
                
                this.cars = [];
                for (let index = 0; index < this.records_per_page; index++) {
                    this.cars.push({index})
                }
                
                let url = '/api/cars?page_type=payments&page=' + this.page;
                for (const key in this.filter_param) {
                    if (this.filter_param[key]) {
                        url += '&' + key + '=' + this.filter_param[key];
                    }
                }
                if (this.filter_like) {
                    url += '&type=like';
                }

                let loader = this.$loading.show();

                this.axios
                .get(url, commonService.get_api_header())
                .then(response => {
                    loader.hide();
                    var res_data = response.data;
                    this.cars = res_data.data;
                    this.total = res_data.total;

                    var start_page = Math.max(1, this.page - 2);
                    var end_page = Math.min(start_page + 4, parseInt(this.total/this.records_per_page) + 1 );
                    this.valid_pages = [];
                    for (let index = start_page; index <= end_page; index++) {
                        this.valid_pages.push(index);
                    }
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
            resetFilter() {
                EventBus.$emit('reset-payment-filter');
            },
            showDetail(car) {
                this.sel_car = null;
                setTimeout(() => {
                    this.sel_car = car;
                    this.submit_payment = false;
                    this.pay_info = {};                    
                }, 200);
            },
            submitPayment() {
                if (!this.pay_info.card_no) return alert('Please input the card number');
                if (!this.pay_info.card_name) return alert('Please input the cardholder name');
                if (!this.pay_info.exp) return alert('Please input the card expiry');
                if (!this.pay_info.cvc) return alert('Please input card cvc number');
                if (!this.pay_info.amount) return alert('Please input amount');

                const thiz = this;
                let loader = this.$loading.show();
                setTimeout(() => {
                    loader.hide();
                    this.submit_payment = true;
                    this.sel_car = {...this.sel_car};
                }, 1000);
            }
        }
    }
</script>


<style lang="stylus" scoped>
#PaymentsPage {
    .status-won, .status-active, .status-fail {
        margin-left: 0;
    }
}
</style>