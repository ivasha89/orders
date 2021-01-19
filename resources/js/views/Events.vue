<template>
    <div>
        <table class="table table-sm table-hover">
            <thead>
                <tr>
                    <th>Id мероприятия</th>
                    <th>Id события</th>
                    <th>Дата</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="event in events" :key="event.id">
                    <td>{{event.showId}}</td>
                    <td>
                        <a :href="'/events/'+event.id+'/places'" >
                            {{event.id}}
                        </a>
                    </td>
                    <td>{{event.momentDate}}</td>
                </tr>
            </tbody>
        </table>

        <FlashMessage :position="'right bottom'"/>
    </div>
</template>
<script>
import moment from 'moment';
export default {
    data: () => ({
            showId: 0,
            events: {},
            portal:'',
            protocol: '',
        }),
    mounted() {
        this.protocol = this.$store.getters.protocol
        this.portal = this.$store.getters.portal
        this.showId = this.$route.params.showId
        this.getEvents(this.showId)
    },
    watch: {
        '$store.getters.protocol': function() {
            this.protocol = this.$store.getters.protocol
        },
        '$store.getters.portal': function() {
            this.portal = this.$store.getters.portal
        },
        protocol: function() {
            this.getEvents(this.showId)
        },
        portal: function() {
            this.getEvents(this.showId)
        }
    },
    methods: {
        getEvents(id) {
            let url = this.protocol+'://'+this.portal+'/shows/'+ id + '/events'
            axios.get(url)
                .then((data) => {
                    this.events = data.data.response;
                    for (let key in this.events) {
                        this.events[key].momentDate = moment(this.events[key].date).format('MMMM Do YYYY, H:mm:ss')
                    }
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