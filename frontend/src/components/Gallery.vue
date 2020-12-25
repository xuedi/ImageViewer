<template>

  <v-container fluid>
    <v-row>
      <v-col v-for="image in images">
        <v-card class="d-flex align-content-start flex-wrap">
          <v-img :src="`http://localhost:8081/thumbs/${image}_400`" :lazy-src="`dummy_tumb.jpg`" aspect-ratio="1" class="grey lighten-2"></v-img>
        </v-card>
      </v-col>
    </v-row>
  </v-container>

</template>

<script>
  import {EventBus} from '../eventBus';

  export default {
    name: 'Gallery',

    data: () => ({
      images: [
        "399f0b641576acd2c9d8badc4d9389299a913c61",
        "0417c03e0fb6694871efcd20464e9df9b70c4198",
        "47b8dbbffeee12a5ee8bf1762199527d9b398dc9",
        "9b8d2a30dbf6ac399e85855f6f5433bab03fd0e8",
        "8a0c4387455aca098891ee00a7b31e8fa781a0e0",
        "e63674538ef9426abece15fb57837c32bc005ef1",
        "1cf103aee56475000e09a5f82ce61a7ef97a623f",
        "a26a7e3b26409ff8883aa56608873aeab2f6eec4",
        "a661359e7636852e6942adcc7973c637023a2a82",
        "9a07d974959406180cb5188d8bdd9cc437c40338",
        "bd9c5439a67adb690e37f493f79e86c4cb940c0d",
        "3a116778e2457fd9bbce22b35e1d3d57bbe17807",
        "d35de2ff7e442a0cc778e4b31edddb75a3daa497",
        "ff2dd456fe978748bd9edece7e2eae0adaa06f9b",
        "36660c680543086343fd5b2a4a3fcc93f3f5f230",
        "2b59c0809fc8dd1e2f1a9d1244f9855fc9288c5b",
        "7a00b60fedeccde121b139f27ee30a1395c8b500",
        "6367ce3b7d7589a1415a36ddf2a1e1cc1c9c4e09",
        "1fb6f5cd3247a40bd887a30eb122298fea38c8db",
        "205f43acf4d567f7faa04b2325e0fec21f6cd3da",
        "06d2ed850bac6801dcc642128aae5612fc34779e",
        "7a88462431e35211d597eceadb529ffb38408f08",
        "98111cdea22a63f54faf071146e7a8b37ca7c8cc",
        "071ef125e35d731696dbd3d81529e79edd6b7d9b",
        "f05c99052f382eb595e2c2e9f5746a985deb9146",
        "614293d239fc0ecd78ecc5b53deb4a6a38932b38",
        "a8332440c41715329d86e7ecd29b2d7310258427",
        "fed229d4e83dd329b957ab7456ca12a4d2b000ba",
        "dc9ec88b4dea8ba76dfad371904f28697edc0129",
        "1e676124604576d8f67cc9e65fccc51eb0b1e04f",
        "91d7ec0fe3bf2162f9c5ebc7a2438d36cdb46514",
        "2122c02cc8d9871aaecea5068d20f4a8b8ea9e0f",
        "7ab14e86da008b4b09bd4cf8c4d4f9f5bad4e5df",
        "d4c3fdc1947b83a43732572d24f688b32a919282",
        "3a7753b98f1d54311b63610d48de8245d271dfa5",
        "bba37740765dfa4ae630abf5434e2762b80f233a",
        "0ebd3292a7cd705cdeb25c6a93c55413b2a6b10b",
      ]
    }),

    methods: {
      updateData(payload) {
        this.images = payload
      }
    },

    mounted() {
      EventBus.$on('loadGallery', link => {
        console.log(`Got an event to load gellery: ${link}`);
        this.updateData(require('../../json_cache/event_' + link + '.json'))
      })
    }
  }
</script>
