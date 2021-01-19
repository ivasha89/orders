<template>
    <div>
        <div class="list-group" v-if="shows">
            <a :href="'/shows/'+show.id+'/events'" class="list-group-item list-group-item-action" v-for="show in shows" :key="show.id">
                {{show.name}}
            </a>
        </div>
        <FlashMessage :position="'right bottom'"/>
    </div>
</template>
<script>
    export default {
        data: () => ({
            protocol: '',
            portal: '',
            showId: 0,
            shows: {},
        }),
        mounted() {
            this.protocol = this.$store.getters.protocol
            this.portal = this.$store.getters.portal
            this.getShows()
        },
        watch: {
            '$store.getters.protocol': function() {
                this.protocol = this.$store.getters.protocol
            },
            '$store.getters.portal': function() {
                this.portal = this.$store.getters.portal
            },
            protocol: function() {
                this.getShows()
            },
            portal: function() {
                this.getShows()
            }
        },
        methods: {
            getShows() {
                let url = this.protocol+'://'+this.portal+'/shows'
                axios.get(url)
                    .then( (data) => {
                        this.shows = data.data.response;
                    })
                    .catch( (error) => {
                        if(error.toString().includes('404')) {
                            error = 'Поставщик билетов не найден'
                        }
                        else if(error.toString().includes('Network Error')) {
                            error = 'Необходимо отключить CORS в браузере'
                        }
                        this.flashMessage.error({
                            title: 'Ошибка',
                            message: error,
                        });
                    });
            }
        }
    }
</script>

<style scoped>

</style>
