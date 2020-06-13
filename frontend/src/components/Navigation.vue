<template>
  <v-container class="mx-auto" width="300">


    <v-list>

      <v-list-item>
        <v-list-item-icon>
          <v-icon>mdi-home</v-icon>
        </v-list-item-icon>
        <v-list-item-title>Home</v-list-item-title>
      </v-list-item>




      <v-list-group no-action sub-group value="true">

        <template v-slot:activator>
          <v-list-item-content>
            <v-list-item-title>Countries</v-list-item-title>
          </v-list-item-content>
        </template>

        <v-list-item v-for="country in countries" :key="country.name" link >
          <v-list-item-title v-text="country.name"></v-list-item-title>
          <v-list-group no-action sub-group value="true">

            <v-list-item v-for="event in events" :key="event.name" link >
              <v-list-item-title v-text="event.name"></v-list-item-title>
            </v-list-item>

          </v-list-group>
        </v-list-item>

      </v-list-group>

    </v-list>




    <v-expansion-panels accordion focusable>
      <v-expansion-panel v-for="country in countries" :key="country.name">
        <v-expansion-panel-header v-text="country.name"></v-expansion-panel-header>
        <v-expansion-panel-content>
          Eventlist
        </v-expansion-panel-content>
      </v-expansion-panel>
    </v-expansion-panels>




  </v-container>
</template>

<script>
export default {
  name: 'Navigation',

  data: () => ({
    drawer: null,
    countries: [
      {name: 'China'},
      {name: 'Czech'},
      {name: 'Denmark'},
      {name: 'England'},
      {name: 'France'},
      {name: 'Germany'},
      {name: 'Greece'},
      {name: 'Ireland'},
      {name: 'Italy'},
      {name: 'Netherlands'},
      {name: 'Poland'},
      {name: 'Romania'},
      {name: 'Serbia'},
    ],
    menu: [
      {name: 'China'},
    ],
  })
}
</script>
