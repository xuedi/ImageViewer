<style>
.event {
  font-size: 14px;
  white-space: nowrap;
}
</style>

<template>

  <v-card class="mx-auto" flat>
    <v-list dense>
      <v-list-item prepend-icon=">mdi-home">
        <v-list-item-icon>
          <v-icon>mdi-home</v-icon>
        </v-list-item-icon>
        <v-list-item-title>Home</v-list-item-title>
      </v-list-item>

      <v-list-group>

        <template v-slot:activator>
          <v-list-item-icon>
            <v-icon>mdi-earth</v-icon>
          </v-list-item-icon>
          <v-list-item-title>Countries</v-list-item-title>
        </template>

        <v-list-group sub-group no-action v-for="location in locations">
          <template v-slot:activator>
            <v-list-item-content>
              <v-list-item-title v-text="location.name"></v-list-item-title>
            </v-list-item-content>
          </template>
          <v-list-item class="pa-0" v-for="event in location.events">
            <v-list-item class="event" v-text="event.name" @click="loadGallery(event.link)"></v-list-item>
          </v-list-item>
        </v-list-group>

      </v-list-group>
    </v-list>
  </v-card>

</template>

<script>
  import locationList from '../../json_cache/locations.json'
  import { EventBus } from '../eventBus';

  export default {
    name: 'Navigation',

    data() {
      return {
        locations: locationList
      }
    },

    methods: {
      loadGallery(link) {
        EventBus.$emit('loadGallery', link);
      }
    }

  }
</script>
