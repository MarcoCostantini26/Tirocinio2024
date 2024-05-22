<div id="app" class="container mt-5">
    <h1 class="text-center">Carica Immagine</h1>
    <hr>
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="mb-3">
                <label for="imageInput" class="form-label">Seleziona un'immagine</label>
                <input type="file" class="form-control" id="imageInput" accept="image/*" name="image" @change="previewImage">
            </div>
            <div class="text-center mb-3">
                <img :src="imageUrl" alt="Anteprima immagine" class="img-fluid" v-if="imageUrl">
            </div>
            <button class="btn btn-primary btn-lg d-block mx-auto" @click="uploadImage" :disabled="!imageUrl">Carica</button>
        </div>
    </div>
</div>

<script>
    const app = Vue.createApp({
        data() {
            return {
                imageUrl: '',
                selectedFile: null 
            };
        },
        methods: {
            previewImage(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.imageUrl = e.target.result;
                        this.selectedFile = file; 
                    };
                    reader.readAsDataURL(file);
                }
            },
            uploadImage() {
                if (this.selectedFile) { 
                    const formData = new FormData();
                    formData.append('image', this.selectedFile); 
                    axios.post('utils/upload.php', formData)
                        .then(response => {
                            console.log(response.data);
                            alert('Immagine caricata con successo!');
                        })
                        .catch(error => {
                            console.error(error);
                            alert('Si è verificato un errore durante il caricamento dell\'immagine.');
                        });
                }
            }
        }
    });

    app.mount('#app');
</script>
