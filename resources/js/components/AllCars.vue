<template>
    <div id="AllCarsPage" class="page-content-detail">
        <template v-if="!sel_car">
        <div class="page-header">
            <span>All Cars</span>
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
            <div class="title-header">
                <div class="action-heart"></div>
                <div class="title">Year</div>
                <div class="title">Make</div>
                <div class="title">Model</div>
                <div class="title">Zip Code</div>
                <div class="title">City</div>
                <div class="title">Runs/Drivers</div>
                <div class="title text-center">Mileage</div>
                <div class="title text-center">Current Offer</div>
                <div class="action-go"></div>
            </div>
            <div class="car-body">
                <div class="car-item" v-for="car in cars" :key="car.index">
                    <div class="action-heart">
                        <a href="javascript:;" class="mif-heart" v-bind:class="{'text-danger': car.is_liked}"  v-on:click="likeCar(car)"></a>
                    </div>
                    <div class="item-data">{{ car.Year }}</div>
                    <div class="item-data">{{ car.Make }}</div>
                    <div class="item-data">{{ car.Model }}</div>
                    <div class="item-data">{{ car.Zip_Code }}</div>
                    <div class="item-data">{{ car.City }}</div>
                    <div class="item-data">{{ car.Does_the_Vehicle_Run_and_Drive }}</div>
                    <div class="item-data text-center">{{ car.Miles }}</div>
                    <div class="item-data text-center">{{ car.Buyers_Quote }}</div>
                    <div class="text-center action-go">
                        <a href="javascript:;" v-on:click="showDetail(car)">
                            <span class="mif-arrow-right"></span>
                        </a>
                    </div>
                    <div class="mobile-item item-data" v-on:click="showDetail(car)">
                        <div class="item-content">
                            <div class="font-weight-bold">{{car.Reference_Number}} &nbsp;&nbsp;{{car.Year}} {{car.Make}} {{car.Model}}</div>
                            <div>{{car.City}} &nbsp;&nbsp; {{car.Zip_Code}}</div>
                            <div class="text-blue">{{car.Buyers_Quote}}</div>
                        </div>
                        <div class="action-heart">
                            <a href="javascript:;">
                                <span class="mif-heart"  v-bind:class="{'text-danger': car.is_liked}"></span>
                            </a>
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
        </template>

        <template v-if="sel_car">
        <div class="car-detail-popup">
            <template v-if="!submit_bid">
                <div class="text-right">
                    <a href="javascript:;" class="btn-close" v-on:click="sel_car = null"><span class="mif-cross-light"></span></a>
                </div>
            
                <div class="detail-content">
                    <div class="car-header">
                        <div class="car-title">{{sel_car.Year}} {{sel_car.Make}} {{sel_car.Model}}</div>
                        <div class="header-label text-blue">CURRENT OFFER</div>
                    </div>
                    <div class="row car-fields">
                        <div class="field-item col-md-3">
                            <div class="item-label">ZIP</div>
                            <div type="text" class="item-value">&nbsp;{{sel_car.Zip_Code}}</div>
                        </div>
                        <div class="field-item col-md-3">
                            <div class="item-label">CITY</div>
                            <div type="text" class="item-value">&nbsp;{{sel_car.City}}</div>
                        </div>
                        <div class="field-item col-md-3">
                            <div class="item-label">RUNS/DRIVES</div>
                            <div type="text" class="item-value">&nbsp;{{sel_car.Does_the_Vehicle_Run_and_Drive}}</div>
                        </div>
                        <div class="field-item col-md-3">
                            <div class="item-label">MILEAGE</div>
                            <div type="text" class="item-value">&nbsp;{{sel_car.Miles}}</div>
                        </div>
                        <div class="field-item col-md-3">
                            <div class="item-label">TITLE</div>
                            <div type="text" class="item-value">&nbsp;{{sel_car.Do_they_have_a_Title}}</div>
                        </div>
                        <div class="field-item col-md-3">
                            <div class="item-label">AIRBAGS DEPLOYED</div>
                            <div type="text" class="item-value">&nbsp;{{sel_car.Airbags_Deployed}}</div>
                        </div>
                        <div class="field-item col-md-3">
                            <div class="item-label">DAMAGE</div>
                            <div type="text" class="item-value">&nbsp;{{sel_car.Is_There_Any_Body_Damage_Broken_Glass_2}}</div>
                        </div>
                        <div class="field-item col-md-3">
                            <div class="item-label">DAMAGE DESCRIPTION</div>
                            <div type="text" class="item-value">&nbsp;{{sel_car.Is_there_any_Body_Damage_Broken_Glass}}</div>
                        </div>
                        <div class="field-item col-md-3">
                            <div class="item-label">BROKEN GLASS</div>
                            <div type="text" class="item-value">&nbsp;{{sel_car.Is_There_Any_Broken_Glass_Windows_etc}}</div>
                        </div>
                        <div class="field-item col-md-3">
                            <div class="item-label">WHEELS MOUNTED</div>
                            <div type="text" class="item-value">&nbsp;{{sel_car.Are_all_the_tires_mounted}}</div>
                        </div>
                        <div class="field-item col-md-3">
                            <div class="item-label">MISSING WHEELS</div>
                            <div type="text" class="item-value">&nbsp;{{sel_car.Which_tires_are_missing}}</div>
                        </div>
                        <div class="field-item col-md-3">
                            <div class="item-label">TIRES INFLATED</div>
                            <div type="text" class="item-value">&nbsp;{{sel_car.Are_All_the_Tires_Inflated}}</div>
                        </div>
                        <div class="field-item col-md-3">
                            <div class="item-label">FLAT TIRES</div>
                            <div type="text" class="item-value">&nbsp;{{sel_car.Which_ones_are_flat}}</div>
                        </div>
                        <div class="field-item col-md-3">
                            <div class="item-label">FIRE/FLOOD/HAIL</div>
                            <div type="text" class="item-value">&nbsp;{{sel_car.Fire_or_Flood_Damage}}</div>
                        </div>
                        <div class="field-item col-md-3">
                            <div class="item-label">MISSING PARTS</div>
                            <div type="text" class="item-value">&nbsp;{{sel_car.Any_Missing_Body_Panels_Interior_or_Engine_Parts}}</div>
                        </div>
                        <div class="field-item col-md-3">
                            <div class="item-label">MECHANICAL ISSUES</div>
                            <div type="text" class="item-value">&nbsp;{{sel_car.What_Kind_of_Mechanical_Issues_Are_There}}</div>
                        </div>
                    </div>
                </div>
                <div class="detail-bottom col-md-8 offset-md-2">
                    <button class="btn btn-default btn-like" v-bind:class="{'is_liked': sel_car.is_liked}" v-on:click="likeCar(sel_car)" >
                        <span class="mif-heart"></span>
                    </button>
                    <div class="action-bar">
                        <input type="number" placeholder="Click to type a bid price" v-model="bid_price">
                        <button class="btn btn-primary" v-on:click="submitBid()">MAKE AN OFFER</button>
                    </div>
                </div>
            </template>
            <template v-if="submit_bid">
                <div class="submit-success">
                    <div class="title">YOUR BID OF $ <span class="text-blue">{{submit_bid}}</span> HAS BEEN SUBMITTED!</div>
                    <div class="img-div">
                        <img src="/img/bid_success.png" alt="">
                    </div>
                    <div class="text-right">
                        <button class="btn btn-primary btn-done" v-on:click="sel_car=null">DONE</button>
                    </div>
                </div>
            </template>
        </div>
        </template>
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
                records_per_page: 10,
                total: '-',
                valid_pages: [],
                filter_param: {},
                filter_like: false,
                filter_string: '',
                sel_car: null,
                bid_price: '',
                submit_bid: '',
            }
        },
        created() {
            const thiz = this;
            EventBus.$on('update-car-filter', function(filter_param) {
                thiz.filter_param = filter_param;
                thiz.filter_string = thiz.filter_param['filter_string'];
                delete thiz.filter_param['filter_string'];
                thiz.refreshPage(1);
            });
            EventBus.$on('update-car-filter-like', function(filter_like) {
                thiz.filter_like = filter_like;
                thiz.filter_param = {};
                thiz.refreshPage(1);
            });
            this.refreshPage(1);
            console.log('created');
        },
        beforeDestroy () {
            EventBus.$off('update-car-filter')
            EventBus.$off('update-car-filter-like')
        },
        mounted() {
            console.log('mounted');
            this.refreshPage(1);
        },
        methods: {
            refreshPage(page) {
                if (!page) page = this.page;
                if (page < 1 || page > parseInt(this.total/this.records_per_page) + 1) return;
                this.page = page;
                this.sel_car = null;
                
                this.cars = [];
                for (let index = 0; index < this.records_per_page; index++) {
                    this.cars.push({index})
                }
                
                let url = '/api/cars?page_type=cars&page=' + this.page;
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
                })
                .catch((error) => {
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
            likeCar(car) {
                let loader = this.$loading.show();
                this.axios
                    .post(`/api/car/like/${car.index}`, {like: (car.is_liked ? '' : true)}, commonService.get_api_header())
                    .then(response => {
                        loader.hide();
                        car.is_liked = !car.is_liked;
                        // this.refreshPage();
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
                EventBus.$emit('reset-car-filter');
            },
            showDetail(car) {
                this.sel_car = car;
                this.bid_price = '';
                this.submit_bid = false;
            },
            submitBid() {
                if (!this.bid_price) return alert('Please type the bid price');
                const thiz = this;
                let loader = this.$loading.show();
                setTimeout(() => {
                    loader.hide();
                    this.submit_bid = this.bid_price;
                    this.sel_car = {...this.sel_car};
                }, 1000);
            }
        }
    }
</script>

<style lang="stylus" scoped>
@media only screen and (max-width: 992px) {
    .car-detail-popup {
        position: fixed !important;
        left: 0;
        top: 70px;
        right: 0;
        bottom: 0;
        z-index: 1111;
        overflow: auto;
        padding: 0;
        .header-label {
            display:none;
        }
        .btn-close {
            top: 10px;
            right: 10px;
        }
        .detail-content {
            padding: 0 !important;
        }
        .car-title {
            width: calc(100% + 50px);
            text-align: center;
            font-size: 25px;
            margin-left: -25px;
            margin-right: -25px;
            margin-top: 30px;
            border-bottom: 2px solid #31AEED;
            padding: 20px 0;
        }
        .detail-bottom {
            position: initial !important;
            .btn-like {
                position: absolute;
                left: 10px;
                top: 10px;
                width: 55px !important;
                height: 55px !important;
            }
            .action-bar {
                width: 100%;
                .btn {
                    padding-left: 15px !important;
                    padding-right: 15px !important;
                }
            }
        }
        .submit-success {
            .title {
                font-size: 25px !important;
            }
            .btn-done {
                margin-right: 0 !important;
            }

        }
    }
}
</style>