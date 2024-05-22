<div id="app" class="container mt-5">
    <h1 class="text-center">Genera Immagine</h1>
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="mb-3">
                <label for="textInput" class="form-label">Inserisci il testo</label>
                <input type="text" class="form-control" id="textInput" v-model="inputText">
            </div>
            <button class="btn btn-primary btn-lg d-block mx-auto" @click="generateImage">Genera Immagine</button>
            <div class="text-center mt-3">
                <img :src="imageUrl" alt="Immagine generata" v-if="imageUrl" class="img-fluid">
            </div>
        </div>
    </div>
</div>

    <script>

        const openai = new OpenAI({
            apiKey: "#",
        });

    const app = Vue.createApp({
        data() {
            return {
                inputText: '',
                imageUrl: ''
            };
        },
        methods: {
            async generateImage() {
                try {
                    const response = await openai.images.generate({
                        model: 'dall-e-3',
                        prompt: this.inputText,
                        n: 1,
                        size: "1024x1024"
                    });
                    const imageUrl = response.data.choices[0].text;
                    this.imageUrl = imageUrl;
                    console.log("Immagine generata:", this.imageUrl);
                } catch (error) {
                    console.error("Errore durante la generazione dell'immagine:", error);
                }
            }
        }
        
    });

    app.mount('#app');
</script>