<div id="app" class="container">
        <form class="mt-5">
            <fieldset>
                <legend>Login</legend>
                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" v-model="formData.email" id="email" class="form-control" placeholder="La tua e-mail" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password:</label>
                    <input type="password" v-model="formData.password" id="password" class="form-control" placeholder="La tua password" required>
                </div>
                <button @click="loginUser" class="btn btn-dark">Login</button>
            </fieldset>
        </form>

        <div v-if="error" class="alert alert-danger mt-3" role="alert">
            {{ error }}
        </div>

        <p class="mt-3 text-center">Non sei ancora registrato? <a title="registrati" class="link-primary" href="register.php">Registrati</a></p>
    </div>
    <script>
        let app = Vue.createApp({
            data() {
                return{    
                    formData: {
                        email: '',
                        password: ''
                    },
                    error: null
                }
            },
            methods: {
                loginUser() {
                    event.preventDefault();
                    fetch('utils/login.php', {
                        method: 'POST',
                        body: JSON.stringify(this.formData),
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            this.error = data.error;
                        } else if (data.success) {
                            window.location.href = "index.php";
                        }
                    })
                    .catch(error => {
                        console.error('Errore durante la richiesta:', error);
                        this.error = "Si è verificato un errore durante il login";
                    });
                }
            }
        });

        app.mount('#app');
    </script>