<div id="app" class="container">
        <form class="mt-5">
            <h2 class="mb-4">Registrazione</h2>
            <div class="form-group">
                <label for="username">Nome utente:</label>
                <input type="text" v-model="formData.username" id="username" class="form-control" placeholder="Inserisci nome utente" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" v-model="formData.email" id="email" class="form-control" placeholder="Inserisci email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" v-model="formData.password" id="password" class="form-control" placeholder="Inserisci password" required>
            </div>
            <div class="form-group">
                <label for="password2">Conferma password:</label>
                <input type="password" v-model="formData.password2" id="password2" class="form-control" placeholder="Ripeti password" required>
            </div>
            <button @click="registerUser" class="btn btn-dark btn-block mt-2">Registrati</button>
        </form>
</div>

    <script>
        let app = Vue.createApp({
            data() {
                return {
                   formData: {
                    username: '',
                    email: '',
                    password: '',
                    password2: ''
                },
                error: null,
                success: null 
                };
            },
            methods: {
                registerUser() {
                    event.preventDefault(); 
                    fetch('utils/registerJSON.php', {
                        method: 'POST',
                        body: JSON.stringify(this.formData),
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log(data);
                        if (data.error) {
                            this.error = data.error;
                            this.success = null;
                        } else if (data.success) {
                            this.success = data.success;
                            this.error = null;
                            window.location.href = "login.php";
                        }
                    })
                    .catch(error => {
                        console.error('Errore durante la richiesta:', error);
                        this.error = "Si è verificato un errore durante la registrazione";
                        this.success = null;
                    });
                }
            }
        });

        app.mount('#app');
    </script>