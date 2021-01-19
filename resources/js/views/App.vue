<template>
    <div>
        <div class="container">
            <h1>Заказать билет</h1>
            <p>
                <router-link :to="{ name: 'shows' }">Список Событий</router-link>
            </p>

            <div class="form-inline">
                <select class="custom-select mb-2 mr-sm-2" id="select" v-model="protocol">
                    <option>Выбрать</option>
                    <option value="http">http</option>
                    <option value="https" selected>https</option>
                </select>

                <input type="text" class="form-control mb-2 mr-sm-2" v-model="portal" value="">
                <button class="btn btn-primary ml-2 mb-2" @click="changeSeller">Поменять поставщика</button>
            </div>
        </div>

        <div class="container mb-3">
            <router-view></router-view>
        </div>
    </div>
</template>
<script>
    export default {
        data: () => ({
            protocol: '',
            portal: '',
        }),
        created() {
            this.protocol = this.$store.state.protocol
            this.portal = this.$store.state.portal
        },
        methods: {
            changeSeller() {
                this.$store.state.protocol = this.protocol
                this.$store.state.portal = this.portal
            }
        }
    }
</script>