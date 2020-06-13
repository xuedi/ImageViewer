<template>

  <v-container fluid>
    <v-row>
      <v-col v-for="image in images">
        <v-card class="d-flex align-content-start flex-wrap">
          <v-img :src="`http://localhost:8081/thumbs/${image}_200`" :lazy-src="`dummy_tumb.jpg`" aspect-ratio="1" class="grey lighten-2"></v-img>
        </v-card>
      </v-col>
    </v-row>
  </v-container>

</template>

<script>
  import {EventBus} from '../eventBus';

  export default {
    name: 'Gellery',

    data: () => ({
      images: [
        '0a1ec2ec43608aec32500fef49d55c7d029afcf3',
        '7ae0c4c18502be8ad1c6488e4c3ea9d38eb036a0',
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
