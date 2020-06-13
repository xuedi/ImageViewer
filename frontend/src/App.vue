<template>
  <v-app>

    <v-app-bar clipped-left dense app color="primary" dark>
      <v-app-bar-nav-icon @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
      <div class="d-flex align-center">
        Gallery
      </div>
      <v-spacer></v-spacer>
      <div class="d-flex align-center">
        <v-text-field dense flat solo-inverted hide-details prepend-inner-icon="mdi-magnify" label="Search"
                      class="hidden-sm-and-down"></v-text-field>
      </div>
      <v-spacer></v-spacer>
      <div class="d-flex align-center">
        Layout:
        <v-icon>mdi-view-list</v-icon>
        <v-icon>mdi-view-module</v-icon>
        <v-icon>mdi-view-comfy</v-icon>
      </div>
    </v-app-bar>

    <v-navigation-drawer v-model="drawer" clipped app>
      <Navigation/>
    </v-navigation-drawer>

    <v-content>
      <Gallery/>
    </v-content>

    <v-bottom-navigation fixed dark>
      Selected tags and other stuff
    </v-bottom-navigation>

  </v-app>
</template>

<script>
  import Gallery from './components/Gallery'
  import Navigation from './components/Navigation'

  export default {
    name: 'App',

    components: {
      Gallery,
      Navigation
    },

    data: () => ({
      drawer: null,
    })
  }
</script>
