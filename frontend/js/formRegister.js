
const registerForm = Vue.component('register-form', {
    data: function () {
        return {
            user:
            {
                firstName: '',
                lastName: '',
            }
        }
    },
    template: '<div><form v-on:submit.prevent="send">' +
        '<div class="form-group"><label for="lastName">Nom</label><br/>' +
        '<input class="form-control" id="lastName" v-model="user.lastName" placeholder="votre nom"><br/></div>' +
        '<div class="form-group"><label for="firstName">Prénom</label><br/>' +
        '<input class="form-control" id="firstName" v-model="user.firstName" placeholder="votre prénom"></div>' +
        '<br/><button class="btn btn-simplon btn-lg">Envoyer</button>' +
        '</form><br/></div>',
    methods: {
        send: function () {
            let datas = { firstName: this.user.firstName, lastName: this.user.lastName };
            fetch('../backend/php/registerEntryPoint.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(datas)
                })
                .then(function (response) {
                    return response.text();
                })
                .then(function (text) {
                    console.log('Request successful', text);
                })
                .catch(function (error) {
                    console.log('Request failed', error);
                });
        },

    }
})

/***** ModelView    *******/
var app = new Vue({
    el: '#app',
    data: {
        courseTitle: 'Formation web dev'
    }
})