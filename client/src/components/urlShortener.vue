<template>
  <div class="url-shortener">
    <h1 class="title">URL Shortener</h1>
    <form @submit.prevent="submitUrl">
      <input v-model="originalUrl" type="text" placeholder="Enter your URL" />
      <button type="submit">Shorten</button>
    </form>

    <div class="shortened" v-if="shortenedUrls.length > 0">
      <ul>
        <li v-for="(shortenedUrl, index) in shortenedUrls" :key="index">
          <p>Original URL: {{ shortenedUrl.originalUrl }}</p>
          <p>Shortened URL: {{ shortenedUrl.shortenedUrl }}</p>
          <button @click="copyToClipboard(shortenedUrl.shortenedUrl)">
            Copy
          </button>
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import Clipboard from "clipboard";

export default {
  data() {
    return {
      originalUrl: "",
      shortenedUrls: [],
    };
  },
  methods: {
    async submitUrl() {
      try {
        const response = await axios.post("http://127.0.0.1:8000/api/urls", {
          originalUrl: this.originalUrl,
        });
        console.log(response);
        const { originalUrl, shortenedUrl } = response.data;
        this.shortenedUrls.push({ originalUrl, shortenedUrl });
        console.log("Shortened URL:", shortenedUrl);
      } catch (error) {
        console.error("Error shortening URL:", error);
      }
    },

    copyToClipboard(shortenedUrl) {
      const textarea = document.createElement("textarea");
      textarea.value = shortenedUrl;
      document.body.appendChild(textarea);
      textarea.select();
      document.execCommand("copy");
      document.body.removeChild(textarea);
      console.log("Copied to clipboard:", shortenedUrl);
    },
    initClipboard() {
      const clipboard = new Clipboard(".copy-button");
      clipboard.on("success", (e) => {
        console.log("Copied to clipboard:", e.text);
      });
      clipboard.on("error", (e) => {
        console.error("Copy to clipboard failed:", e.action);
      });
    },
  },
  mounted() {
    this.initClipboard();
  },
};
</script>

<style scoped>
.url-shortener {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  background-color: #6366f1;
  padding: 40px 60px 60px 60px;
  border-radius: 13px;
  filter: drop-shadow(0px 5px 15px #5b21b6);
  margin-bottom: 30px;
}

.title {
  font-size: 1.8rem;
  margin-bottom: 1.5rem;
  color: #fff;
}

form {
  display: flex;
  align-items: center;
  gap: 20px;
}

input {
  padding: 0.5rem;
  width: 300px;
  border-radius: 11px;
  border: none;
  background-color: #fff;
  padding: 15px 20px;
  color: #64748b;
}

button {
  padding: 12px 20px;
  background-color: #10b981;
  color: #fff;
  border: none;
  cursor: pointer;
  transition: background-color 0.3s ease;
  border-radius: 13px;
}

button:hover {
  background-color: #047857;
}

.shortened {
  padding: 20px;
  background-color: #6366f1;
  border-radius: 13px;
}
</style>
