<template>
    <div>
        <div class="container">
            <h1>Заказать товар</h1>
            <div class="form-row">
                <div class="col-md-4 mb-3">
                    <label for="validationCustom01">Имя</label>
                    <input type="text" class="form-control" id="validationCustom01" v-model="name" placeholder="Иван">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="validationCustom02">Фамилия</label>
                    <input type="text" class="form-control" id="validationCustom02" placeholder="Петров" v-model="lastName">
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-4 mb-3">
                    <label for="validationCustom03">Телефон</label>
                    <input type="text" class="form-control" id="validationCustom03" v-model="phone" maxlength="10">
                    <small class="text-muted">укажите цифры после +7</small>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="validationCustom04">Тариф</label>
                    <select class="custom-select" id="validationCustom04" v-model="tarif">
                        <option v-for="(tarif, index) in tarifs" :key="index" :value="tarif">{{tarif.name}} {{tarif.cost}}</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="validationCustom05">Дата доставки</label>
                    <select class="custom-select" id="validationCustom05" v-model="date">
                        <option selected disabled value="">Выбрать дату</option>
                        <option v-for="(date, index) in dates" :key="index">{{date}}</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="validationCustom05">Адрес доставки</label>
                    <select class="custom-select" id="validationCustom05" v-model="address">
                        <option v-for="(address, index) in addresses" :key="index">{{address}}</option>
                    </select>
                </div>
            </div>
            <button class="btn btn-primary mb-5" @click="makeOrder">Заказать</button>
            <ul class="list-group mb-3">
                <li class="list-group-item" v-for="(order, index) in orders" :key="index">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1">{{order.name}}</h5>
                        <small>{{order.date}}</small>
                    </div>
                    <p class="mb-1">{{order.phone}}</p>
                    <small>{{order.address}}</small>
                </li>
            </ul>
        </div>
        <FlashMessage :position="'right bottom'"></FlashMessage>
    </div>
</template>
<script>
import moment from 'moment';
    export default {
        data: () => ({
            token: $('meta[name="csrf-token"]').attr('content'),
            rescan: 1,
            name: '',
            lastName: '',
            phone: '',
            tarifs: [],
            tarif: {},
            orders: [],
            dates: ['выберите тариф'],
            date: '',
            address: '',
            addresses: [
                'г. Нижний Новгород, ул. Ровная, д. 3',
                'г. Урюпинск, ул. Долгая, д. 321',
                'г. Юрюзань, ул. Капушина, д. 13',
            ],
            validation: 'Укажите',
        }),
        mounted() {
            this.getTarifs(),
            this.getOrders()
        },
        watch: {
            tarif: function() {
                this.checkTarif()
            },
            rescan: function() {
                this.getOrders()
            }
        },
        methods: {
            getTarifs() {
                $.ajax({
                    _token: this.token,
                    url: 'tarifs',
                    method: 'get',
                    dataType: 'json',
                    success: function(data) {
                        console.log(data)
                        this.tarifs = data
                    }.bind(this)
                })
            },
            getOrders() {
                $.ajax({
                    _token: this.token,
                    url: 'orders',
                    method: 'get',
                    dataType: 'json',
                    success: function(data) {
                        this.orders = data
                    }.bind(this)
                })
                this.orders.forEach(element => {
                    element.date = moment(element.date).fromNow()
                });
            },
            makeOrder() {
                if (this.name < 3) this.validation += ' имя,'
                if (this.lastName < 3) this.validation += ' фамилию,'
                if (this.phone.length != 10) this.validation += ' телефон корректно,'
                if (!this.date) this.validation += ' дату доставки,'
                if (!this.address) this.validation += ' адресс доставки,'
                if (this.validation.length > 7) {
                    this.flashMessage.error({
                        title: 'Ошибка',
                        message: this.validation.slice(0, -1),
                    })
                    this.validation = 'Укажите'
                }
                else {
                    $.ajax({
                        url: 'makeorder',
                        method: 'post',
                        dataType: 'json',
                        data: {
                            _token: this.token,
                            name: this.name,
                            lastName: this.lastName,
                            phone: this.phone,
                            date: this.date,
                            address: this.address,
                        },
                        success: function(data) {
                            if (data.success != 'undefined') {
                                this.flashMessage.success({
                                    title: 'Удача',
                                    message: data.success,
                                })
                                this.rescan++ 
                            }
                            else {
                                this.flashMessage.error({
                                    title: 'Ошибка',
                                    message: data.error,
                                })
                            }
                        }.bind(this),
                    })
                }
            },
            checkTarif() {
                let now = (new Date()).getTime()
                let dates = this.tarif.days.split(";")
                this.dates = [
                    moment(now).add(dates[0], 'd').format('DD-MM-YYYY'),
                    moment(now).add(dates[1], 'd').format('DD-MM-YYYY'),
                    moment(now).add(dates[2], 'd').format('DD-MM-YYYY')
                ]
            },
        }
    }
</script>