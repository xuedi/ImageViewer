<template>
  <v-container>

    many images

  </v-container>
</template>

<script>
  import {EventBus} from '../eventBus';

  EventBus.$on('loadGallery', link => {
    console.log(`Got an event to load gellery: ${link}`)
  });

  export default {
    name: 'Gellery',

    data: () => ({
      //
    })
  }
</script>
