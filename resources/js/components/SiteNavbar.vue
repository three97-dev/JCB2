<template>
<div id="SiteNavbar">
    <nav class="navbar navbar-expand-lg">
        <div class="collapse navbar-collapse">
            <div class="navbar-userinfo">
                <div class="navbar-notification logo-wrapper">
                    <img v-bind:src="avatar" alt="logo">
                </div>
                <div class="navbar-username">
                    {{username}} <span class="mif-arrow-drop-down drop-down" @click="showDropdown"></span>
                    <div class="dropdown-action-menu" v-if="showActions">
                        <div class="dropDownMenu-header">
                            <div><span class="cursor-pointer" @click="stateInitialize">Menu</span> {{submenu}}</div>
                            <div class="close" @click="showDropdown"><span class="mif-cross-light"></span></div>
                        </div>
                        <div class="content" v-if="!showPaymentSettings && !showProfileSettings && !showAddressSettings">
                            <div class="content-row" @click="showProfile">
                                <span class="mif-user"></span>
                                &nbsp;&nbsp;Profile
                            </div>
                            <div class="content-row" @click="showPayment">
                                <span class="mif-credit-card"></span>
                                &nbsp;&nbsp;Payment Method
                            </div>
                            <div class="content-row" @click="showAddress">
                                <span class="mif-my-location"></span>
                                &nbsp;&nbsp;Address
                            </div>
                        </div>
                        <div class="content" v-if="showProfileSettings" style="cursor: unset;">

                            <div class="profile-wrapper">
                                <div class="row">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="input-label">Name</label>
                                            <input type="text" placeholder="Input" class="input" v-model="username" />
                                        </div>
                                        <!-- <div class="form-group">
                                            <label class="input-label">Last Name</label>
                                            <input type="text" placeholder="Input" class="input" />
                                        </div> -->
                                        <div class="form-group">
                                            <label class="input-label">Company Name</label>
                                            <input type="text" placeholder="Input" class="input" v-model="companyName" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <!-- <a class="btn" @click="toggleShow">set avatar</a>
                                        <my-upload field="img"
                                            ki="0"
                                            :width="300"
                                            :height="300"
                                            url="/upload"
                                            :params="params"
                                            :headers="headers"
                                            lang-type="en"
                                            :value="false"
                                            imgFormat="png"
                                            :allowImgFormat="['gif','jpg','png']"
                                            ></my-upload> -->
                                        <input type="file" @change="filesChange($event.target.files)" class="profile_uploader" accept="image/*" ref="myfile"/>
                                        <div class="profile_wrapper" :style="'background-image:' + image" @click="clickUploader">
                                            <div>Click to add</div>
                                        </div>
                                    </div>
                                    <div class="accountActionBtn">
                                        <button class="btn btn-primary action-button" @click="saveProfile">SAVE</button>
                                    </div>

                                </div>
                            </div>

                        </div>
                        <div class="content" v-if="showPaymentSettings" style="cursor: unset;">
                            <div class="content-row">
                                <div>Payment Method</div>
                                <!-- <button class="card-action-btn action-button unset-default-btn" @click="unsetPayment" v-if="default_id && !paymentEditing">Unset Payment Method</button> -->
                            </div>
                            <br>
                            <div class="row" v-if="!paymentEditing">
                                <div class="col-md-10">
                                    <div class="row">
                                        <div class="col-md-6" v-for="(payment, i) in payments" :key="i">
                                            <div class="card-info" :class="{'selected' : default_id == payment.id}">
                                                <div class="row card-content">
                                                    <div class="col-md-5">
                                                        <div class="card-placeholder-image">
                                                            <img :src="cardImageLoc + lower(payment.card.brand) + '.png'" alt="">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-7">
                                                        <div class="card-last-4number">
                                                            {{ upper(payment.card.brand) }} {{payment.card.last4}}
                                                        </div>
                                                        <div class="card-expire">
                                                            Expires {{payment.card.exp_month | monthFilter}} / {{payment.card.exp_year}}
                                                        </div>
                                                    </div>
                                                    <div class="markup-default" v-if="default_id == payment.id">Default Payment</div>
                                                </div>
                                                <hr class="card-divider"/>
                                                <div class="card-action">
                                                    <button class="card-action-btn" @click="setDefaultPayment(payment.id)" v-if="default_id != payment.id">Set as Default</button>
                                                    <button class="card-action-btn" @click="deletePayment(payment.id)">Remove</button>
                                                    <button class="card-action-btn" @click="editCard(payment.id)">Edit</button>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card-info add-card" @click="editCard(null)">
                                                <div style="margin: auto 0; text-align: center">
                                                    <span class="mif-plus" style="font-weight: 300"></span> &nbsp;Add Payment Method
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" v-if="paymentEditing">
                                <div class="col-md-12">
                                    <div class="payment-editing">
                                        <div class="card-placeholder-image">
                                            <img :src="cardImageLoc + lower(cardType) + '.png'" alt="">
                                        </div>
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="row">
                                                    <div class="col-md-12 field-item">
                                                        <div class="item-label">Card Number</div>
                                                        <div id="example1-card-number" class="input empty stripe-elements-div" v-if="paymentCreate"></div>
                                                        <input type="text" class="item-value disabled" disabled v-model="pay_info.card_no" placeholder="1234 1234 1234 1234" v-else />
                                                        <div role="alert" class="stripe-elements-error-message-div">{{stripe_card_errors}}</div>
                                                    </div>
                                                    <div class="col-md-12 field-item">
                                                        <div class="item-label">Cardholder Name</div>
                                                        <input type="text" class="item-value" v-model="pay_info.card_name" placeholder="Your name"  onkeypress="return /[a-z ]/i.test(event.key)" />
                                                    </div>
                                                    <div class="col-md-6 field-item">
                                                        <div class="item-label">Card Expiry</div>
                                                        <div id="example1-card-expiry" class="input empty stripe-elements-div" v-if="paymentCreate"></div>
                                                        <masked-input class="item-value" v-model="pay_info.exp" mask="11/11" placeholder="MM/YY"  v-else/>
                                                        <div role="alert" class="stripe-elements-error-message-div">{{stripe_expiry_errors}}</div>
                                                    </div>
                                                    <div class="col-md-6 field-item">
                                                        <div class="item-label">CVC</div>
                                                        <div id="example1-card-cvc" class="input empty stripe-elements-div" v-if="paymentCreate"></div>
                                                        <input type="text" class="item-value disabled" disabled v-model="pay_info.cvc" placeholder="XXX" v-else/>
                                                        <div role="alert" class="stripe-elements-error-message-div">{{stripe_cvc_errors}}</div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="content" v-if="showAddressSettings" style="cursor: unset;">

                            <tabs>
                                <tab name="Primary Address">
                                    <div class="address_form">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="form-group" style="display: flex;">
                                                    <label class="input-label" style="float: left; width: unset;">Address Name: </label>
                                                    <div class="input" >Primary Address</div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="input-label" style="float: left; width: unset;">Street Address </label>
                                                            <input type="text" placeholder="Input" class="input" v-model="shippingAddress.street" />
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="input-label" style="float: left; width: unset;">Suite/Apt/P.O. Box </label>
                                                            <input type="text" placeholder="Input" class="input" v-model="shippingAddress.suite" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">

                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="input-label" style="float: left; width: unset;">City/Town </label>
                                                            <input type="text" placeholder="Input" class="input" v-model="shippingAddress.city" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="input-label" style="float: left; width: unset;">State/Province </label>
                                                            <input type="text" placeholder="Input" class="input" v-model="shippingAddress.state" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="input-label" style="float: left; width: unset;">Zip/Postal Code </label>
                                                            <input type="text" placeholder="Input" class="input" v-model="shippingAddress.code" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <button class="btn btn-primary action-button address-btn" @click="saveAddress">SAVE</button>
                                            </div>
                                        </div>

                                    </div>
                                </tab>
                                <tab name="Address2">
                                    <div class="address_form">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="form-group" style="display: flex;">
                                                    <label class="input-label" style="float: left; width: unset;">Address Name: </label>
                                                    <div class="input" >Address2</div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="input-label" style="float: left; width: unset;">Street Address </label>
                                                            <input type="text" placeholder="Input" class="input" v-model="billingAddress.street" />
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="input-label" style="float: left; width: unset;">Suite/Apt/P.O. Box </label>
                                                            <input type="text" placeholder="Input" class="input" v-model="billingAddress.suite" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">

                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="input-label" style="float: left; width: unset;">City/Town </label>
                                                            <input type="text" placeholder="Input" class="input" v-model="billingAddress.city" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="input-label" style="float: left; width: unset;">State/Province </label>
                                                            <input type="text" placeholder="Input" class="input" v-model="billingAddress.state" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="input-label" style="float: left; width: unset;">Zip/Postal Code </label>
                                                            <input type="text" placeholder="Input" class="input" v-model="billingAddress.code" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <button class="btn btn-primary action-button address-btn" @click="saveAddress">SAVE</button>
                                            </div>
                                        </div>

                                    </div>
                                </tab>
                                <tab name="+ New">
                                    <div class="address_form">
                                        Coming Soon

                                    </div>
                                </tab>
                            </tabs>

                        </div>
                        <div class="signout" @click="logout" v-if="!showPaymentSettings && !showProfileSettings && !showAddressSettings">
                            Sign Out
                        </div>

                        <div class="signout" v-if="paymentEditing">
                            <button class="btn action-button cancel-button" @click="cancelEditing">Cancel</button>
                            <button class="btn btn-primary action-button" @click="submitPaymentTest">SAVE</button>
                        </div>
                    </div>
                </div>

            </div>
            <div class="navbar-nav">
                <div class="nav-wrapper">
                <router-link to="/" class="nav-item nav-link" @click="dropDownMenu = false" v-bind:class="{'active': $route.name == 'home'}">All Cars</router-link>
                <router-link to="/bids" class="nav-item nav-link" @click="dropDownMenu = false" v-bind:class="{'active': $route.name == 'bids'}">Bids</router-link>
                <router-link to="/schedulings" class="nav-item nav-link" @click="dropDownMenu = false" v-bind:class="{'active': $route.name == 'schedulings'}">Scheduling</router-link>
                <router-link to="/payments" class="nav-item nav-link" @click="dropDownMenu = false" v-bind:class="{'active': $route.name == 'payments'}">Payment</router-link>
                <router-link to="/reports" class="nav-item nav-link" @click="dropDownMenu = false" v-bind:class="{'active': $route.name == 'reports'}">Reports</router-link>
                </div>
            </div>
            <div class="navbar-notification"><span class="mif-bell"></span></div>
        </div>
        <div class="navbar-mobile d-lg-none w-100">
            <div class="navbar-mobile-content">
                <img class="user-avatar" v-bind:src="avatar" alt="logo">

                <div class="nav-mobile-title" v-if="$route.name=='home'">All Cars</div>
                <div class="nav-mobile-title" v-if="$route.name=='bids'">Your Bids</div>
                <div class="nav-mobile-title" v-if="$route.name=='schedulings'">Scheduling</div>
                <div class="nav-mobile-title" v-if="$route.name=='payments'">Your Payments</div>
                <div class="nav-mobile-title" v-if="$route.name=='reports'">Reports</div>

                <b-navbar-toggle target="navbar-toggle-collapse" class="btn-mobile-menu">
                    <span class="mif-menu"></span>
                </b-navbar-toggle>
            </div>


            <b-collapse id="navbar-toggle-collapse" is-nav >
                <b-navbar-nav class="ml-auto">
                    <b-nav-item href="/"  v-bind:class="{'active': $route.name == 'home'}">All Cars</b-nav-item>
                    <b-nav-item href="/bids"  v-bind:class="{'active': $route.name == 'bids'}">Bids</b-nav-item>
                    <b-nav-item href="/schedulings"  v-bind:class="{'active': $route.name == 'schedulings'}">Scheduling</b-nav-item>
                    <b-nav-item href="/payments"  v-bind:class="{'active': $route.name == 'payments'}">Payment</b-nav-item>
                    <b-nav-item href="/reports"  v-bind:class="{'active': $route.name == 'reports'}">Reports</b-nav-item>
                </b-navbar-nav>
            </b-collapse>
        </div>
    </nav>

</div>
</template>

<script>
import EventBus from '../event-bus';
import ImageUpload from './ImageUpload'
import CommonService from '../services/CommonService';
const commonService = new CommonService();
import MaskedInput from 'vue-masked-input';
import 'vuetify/dist/vuetify.min.css'

export default {
    components:{
        MaskedInput,
        ImageUpload
    },
    data() {
        return {
            username: '',
            companyName: '',
            avatar : '/img/avatar.png',
            showActions: false,
            showProfileSettings: false,
            showPaymentSettings: false,
            showAddressSettings: false,
            paymentEditing: false,
            paymentCreate: false,
            show: false,
			params: {
				token: '123456798',
				name: 'avatar'
			},
			headers: {
				smail: '*_~'
			},
			imgDataUrl: '',

            // stripe
            pay_info : {},
            stripe: '',
            Card: '',
            submit_payment1: false,
            stripe_card_errors: '',
            stripe_expiry_errors: '',
            stripe_cvc_errors: '',
            preventPayButton: true,
            initPreventPay: true,
            initialized: false,
            stripeApiKey: process.env.MIX_Stripe_Api_PublishKey,
            customer_id: '',
            payments: [],
            default_id: '',
            editingCard: '',
            sel_payment: {},
            autoSetDefault: false,
            cardType: 'generic',
            cardImageLoc: '/img/card-logos/CreditCardLogos_',
            billingAddress: {street: '', city: '', state: '', code: '', suite: ''},
            shippingAddress: {street: '', city: '', state: '', code: '', suite: ''},
        };
    },
    mounted() {
        this.username = commonService.get_auth_name();
        this.avatar = commonService.get_auth_avatar();
        this.showActions = false;

        const stripeApiKey = this.stripeApiKey;
        console.log(stripeApiKey);
        let stripeScript = document.createElement('script');
        stripeScript.setAttribute('src', 'https://js.stripe.com/v3/');
        stripeScript.onload = () => {
            this.stripe = Stripe(stripeApiKey);
        };

        document.head.appendChild(stripeScript);
    },
    watch: {
        $route (to_route, from_route) {
            this.username = commonService.get_auth_name();
            this.avatar = commonService.get_auth_avatar();
        }
    },
    computed: {
        submenu() {
            return this.showPaymentSettings? " > Payment Method" : this.showProfileSettings? " > Profile" : this.showAddressSettings? " > Address" :  "";
        },
        image() {
            let default_image = 'img/profile-icon-9.png';
            let image = !this.imgDataUrl? default_image : "img/profiles/" + this.imgDataUrl;
            return "url('"+image + "')";
        }

    },
    filters: {
        monthFilter: function(month) {
            if(month<10) return "0"+ month;
            return month;
        },
    },
    methods: {
        toggleShow() {

            this.show = !this.show;
        },

        logout() {
            console.log("logout")
            let loader = this.$loading.show();
            this.axios
                .get(`/api/logout`)
                .then(response => {
                    loader.hide();
                    this.$router.push('/logout');
                });
        },
        showDropdown(flag) {
            this.showActions = !this.showActions;
            this.showPaymentSettings = false;
            this.paymentEditing = false;
            EventBus.$emit('uncheckAll', !this.showActions);
        },
        stateInitialize() {
            this.showPaymentSettings = false;
            this.showProfileSettings = false;
            this.showAddressSettings = false;
            this.paymentEditing = false;
        },
        showProfile(flag) {
            if(!this.showProfileSettings) {
                let loader = this.$loading.show();
                let that = this;
                this.axios
                    .get(`/api/getProfile`, commonService.get_api_header())
                    .then(response => {
                        loader.hide();
                        let user = response.data.user;
                        this.imgDataUrl = user.photo;
                        this.username = user.username;
                        this.companyName = user.companyName;
                    });
            }
            this.showProfileSettings = !this.showProfileSettings;
        },
        showPayment(flag) {
            this.showPaymentSettings = !this.showPaymentSettings;
            this.listStripePaymentMethods();
        },
        showAddress(flag) {
            if(!this.showAddressSettings) {
                let loader = this.$loading.show();
                let that = this;
                this.axios
                    .get(`/api/getAddress`, commonService.get_api_header())
                    .then(response => {
                        loader.hide();
                        let address = response.data;
                        that.billingAddress = address.billingAddress;
                        that.shippingAddress = address.shippingAddress;
                    });
            }
            this.showAddressSettings = !this.showAddressSettings;
        },
        editCard(card = null) {
            this.paymentEditing = true;

            if(card == null)
            {
                this.paymentCreate = true;
                setTimeout(() => {
                    this.initialize();
                    this.initStripe();
                }, 100);
                this.cardType = "generic";
            }
            else {
                this.paymentCreate = false;
                this.editingCard = card;
                let payment = this.payments.filter(payment => payment.id == card);
                payment = payment[0];
                let exp_month = payment.card.exp_month < 10? "0" + payment.card.exp_month: payment.card.exp_month;
                this.cardType = payment.card.brand;
                this.pay_info = {
                    card_no : "**** **** **** " + payment.card.last4,
                    card_name : payment.billing_details.name,
                    exp : exp_month + "/" + (payment.card.exp_year-2000),
                    cvc: "XXX"
                };
            }

        },

        cancelEditing() {
            this.paymentEditing = false;
            this.stripe_card_errors = this.stripe_cvc_errors = this.stripe_expiry_errors = '';
        },

        initialize() {
            this.pay_info = {card_name: "", card_no: "", cvc: "", exp: ""};
        },
        listStripePaymentMethods() {
            let that = this;
            let loader = this.$loading.show();
            this.axios.post('/api/payments/stripe/listPayments', {},commonService.get_api_header())
                .then(function (response) {
                    that.payments = response.data.methods.data;
                    that.default_id = response.data.methods.payment_method;
                    // if(response.data.methods.data.length == 1)
                    //     that.setDefaultPayment(response.data.methods.data[0].id);
                    if(that.autoSetDefault)
                        that.setDefaultPayment(response.data.methods.data[0].id);
                    that.autoSetDefault = false;
                    loader.hide();
                }).catch(function (error) {
                    console.log(error);
                    alert(error);
                });
        },
        setDefaultPayment(payment) {
            this.default_id = payment;
            let that = this;
            this.axios.post('/api/payments/stripe/setDefaultPayment', {payment: payment},commonService.get_api_header())
                .then(function (response) {

                }).catch(function (error) {
                    console.log(error);
                    alert(error);
                });
        },
        preventPay: function() {
            if (this.stripe_card_errors || this.stripe_cvc_errors || this.stripe_expiry_errors) {
                this.preventPayButton = true;
            } else {
                this.preventPayButton = false;
            }
        },
        initStripe: function() {
            let elements = this.stripe.elements();
            let Style = {
                    base: {
                    color: '#32325d',
                    paddingLeft: "5px",
                        '::placeholder': {
                        color: '#B9B9B9',
                    },
                }
            };

            var elementStyles = {
                base: {
                    // fontSize: '14px',
                    border:"none",
                    borderBottom: "1px solid #269A8E",
                    fontWeight: 500,
                    padding: "5px 0",
                    outline: "none",
                    width:"100%",
                    font: "normal normal normal 14px/17px Lato",
                    color: '#363636',
                    fontFamily: 'Source Code Pro, Consolas, Menlo, monospace',
                    fontSize: '16px',
                    // backgroundColor: "#e3e3e3",
                    fontSmoothing: 'antialiased',
                    '::placeholder': {
                        color: '#B9B9B9',
                    },
                    ':-webkit-autofill': {
                        color: '#B9B9B9',
                    },
                },
                invalid: {
                    color: '#E25950',
                    '::placeholder': {
                        color: '#FF1744',
                    },
                },
            };

            var elementClasses = {
                focus: 'focused',
                empty: 'empty',
                invalid: 'invalid',
            };
            let that = this;
            this.Card = elements.create('cardNumber', {
                style: elementStyles,
                classes: elementClasses,
            });
            this.Card.mount('#example1-card-number');
            this.Card.on('change', ({error}) => {
                if (error) {
                    that.stripe_card_errors = error.message;
                } else {
                    that.stripe_card_errors = '';
                }
                that.preventPay();
            });
            var cardExpiry = elements.create('cardExpiry', {
                style: elementStyles,
                classes: elementClasses,
            });
            cardExpiry.mount('#example1-card-expiry');
            cardExpiry.on('change', ({error}) => {
                if (error) {
                    that.stripe_expiry_errors = error.message;
                } else {
                    that.stripe_expiry_errors = '';
                }
                that.preventPay();
            });
            var cardCvc = elements.create('cardCvc', {
                style: elementStyles,
                classes: elementClasses,
            });
            cardCvc.mount('#example1-card-cvc');
            cardCvc.on('change', ({error}) => {
                if (error) {
                    that.stripe_cvc_errors = error.message;
                } else {
                    that.stripe_cvc_errors = '';
                }
                that.preventPay();
            });
        },
        submitPaymentTest() {
            let that = this;
            if(!this.pay_info.card_name) {
                alert('card name is empty');
                return;
            }
            if(!this.paymentCreate) {
                var str = this.pay_info.exp;
                let total = 0;
                for (var i = 0; i < str.length; i++) {
                    if(str.charAt(i) == "_") total++;
                }
                if(total>0 || str == '') return alert('card expiry date is not valid');
                var dt = str.split('/');

                if(parseInt(dt[0]) > 12) return alert('expiry month format error');
                if(parseInt(dt[1]) < 21) return alert('expiry year error');
                this.updatePayment();
            }

            else {
                let loader = this.$loading.show();
                this.axios.post('/api/payments/stripe/createCustomer', {},commonService.get_api_header())
                    .then(function (response) {
                        that.customer_id = response.data.customer_id;
                        that.axios.post('/api/payments/stripe/setupIntent', {
                            'customer' : response.data.customer_id,
                        },commonService.get_api_header())
                        .then(function (response) {
                            loader.hide();
                            that.confirmCardSetup(response.data.Intent.Secret);
                        }).catch(function (error) {
                            console.log(error);
                            alert(error);
                            loader.hide();
                        });
                    }).catch(function (error) {
                        console.log(error);
                        alert(error);
                        loader.hide();
                    });
            }

            // this.submit_payment1 = true;


        },
        confirmCardSetup(clientSecret) {
            let loader = this.$loading.show();
            let that = this;
            this.stripe.confirmCardSetup(clientSecret, {
                payment_method: {
                    card: this.Card,
                    billing_details: {
                        name: this.pay_info.card_name,
                    },
                },
                // setup_future_usage: 'off_session'
                }).then(function(result) {
                loader.hide();
                console.log(result)
                if (result.error) {
                    // console.log('payment error' + result.error.message);
                    alert("Card Information isn't correct!");
                } else {
                    if (result.setupIntent.status === 'succeeded') {
                        loader.hide();
                        var conf = confirm("Would you like to make this your default payment method?");
                        if(conf){
                            that.autoSetDefault = true;
                        }
                        that.listStripePaymentMethods();
                        that.paymentEditing = false;

                    } else {
                        console.log('payment error');
                    }
                }
            });
        },
        inputExp(value) {
            console.log(value)
        },
        updatePayment() {
            let that = this;
            let loader = this.$loading.show();
            this.axios.post('/api/payments/stripe/updatePayment', {payment: this.editingCard, name: this.pay_info.card_name, exp: this.pay_info.exp },commonService.get_api_header())
                .then(function (response) {
                    that.paymentEditing = false;
                    that.listStripePaymentMethods();
                    loader.hide();
                }).catch(function (error) {
                    console.log(error);
                    alert(error);
                    loader.hide();
                });
        },
        deletePayment(payment) {
            var r = confirm("Are you really gonna delete payment method?");
            if(r) {
                let that = this;
                let loader = this.$loading.show();
                this.axios.post('/api/payments/stripe/deletePayment', {payment: payment},commonService.get_api_header())
                    .then(function (response) {
                        that.listStripePaymentMethods();
                        loader.hide();
                    }).catch(function (error) {
                        console.log(error);
                        alert(error);
                        loader.hide();
                    });
            }
        },
        unsetPayment() {
            this.default_id = '';
            this.axios.get('/api/payments/stripe/unsetPayment', commonService.get_api_header())
                .then(function (response) {

                }).catch(function (error) {
                    console.log(error);
                    alert(error);
                    loader.hide();
                });
        },
        lower(cardType) {
            let card = cardType.toLowerCase();
            if(card != 'amex' && card != 'discover' && card != 'mastercard' && card != 'visa')
                return 'generic';
            return card;
        },
        upper(cardType) {
            let card = this.lower(cardType);
            if(card == 'amex') return "AMEX";
            if(card == 'discover') return "DISC";
            if(card == 'mastercard') return "MAST";
            if(card == 'visa') return "VISA";
            return "OTHER";
        },
        async setImage(image) {
            const res = await this.$axios.patch('/profile', { image })
            if (res) {
                this.$awn.success('Profile picture updated successfully')
            } else {
                this.$awn.warning('Unable to upload profile picture!')
            }
            this.image = image
        },
        getImageUrl(image) {
            return image
        },
        filesChange(file) {
            console.log(file)
            const formData = new FormData();
            if(!file || !file.length) return;
            formData.append("form", file[0]);
            let loader = this.$loading.show();
            let that = this;
            this.axios.post('/api/uploadPhoto', formData,commonService.get_api_header())
                .then(function (response) {
                    loader.hide();
                    that.imgDataUrl = response.data;
                }).catch(function (error) {
                    console.log(error);
                    alert(error);
                    loader.hide();
                });
        },
        clickUploader() {
            const elem = this.$refs.myfile
            elem.click()
        },
        saveProfile() {
            let loader = this.$loading.show();
            let that = this;
            this.axios.post('/api/saveProfile', {username: this.username, companyName: this.companyName, photo: this.imgDataUrl},commonService.get_api_header())
                .then(function (response) {
                    loader.hide();
                    that.showProfileSettings = false;
                }).catch(function (error) {
                    console.log(error);
                    alert(error);
                    loader.hide();
                });
        },
        saveAddress() {
            let loader = this.$loading.show();
            let that = this;
            this.axios.post('/api/saveAddress', {billingAddress: this.billingAddress, shippingAddress: this.shippingAddress},commonService.get_api_header())
                .then(function (response) {
                    loader.hide();
                    that.showAddressSettings = false;
                }).catch(function (error) {
                    console.log(error);
                    alert(error);
                    loader.hide();
                });
        }
    },

}
</script>
