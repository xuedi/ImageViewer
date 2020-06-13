<template>

    <v-card class="mx-auto" width="300" flat>
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

          <v-list-group sub-group no-action v-for="country in countries" :key="i">
            <template v-slot:activator>
              <v-list-item-content>
                <v-list-item-title v-text="country.name"></v-list-item-title>
              </v-list-item-content>
            </template>
            <v-list-item v-for="(event, i) in country.events" :key="i">
              <v-list-item-title v-text="event[0]"></v-list-item-title>
              <v-list-item-action>
                <v-icon v-text="event[1]"></v-icon>
              </v-list-item-action>
            </v-list-item>
          </v-list-group>

        </v-list-group>
      </v-list>
    </v-card>

</template>

<script>
export default {
  name: 'Navigation',

  // todo get country data from json file (php-cli prebuild)
  data: () => ({
    drawer: null,
    countries: [
      {
        name: 'China',
        events: [
          ['2001-00-00 Something', 'link-id-3'],
          ['2010-12-24 Christmas', 'link-id-2'],
          ['2019-01-01 Beijing BBQ', 'link-id-1'],
          ['2020-06-13 CodingAtHole', 'link-id-4'],
        ],
      },
      {
        name: 'Czech',
        events: [
          ['2001-00-00 Something', 'link-id-3'],
          ['2010-12-24 Christmas', 'link-id-2'],
        ],
      },
      {
        name: 'Denmark',
        events: [
          ['2001-00-00 Something', 'link-id-3'],
          ['2010-12-24 Christmas', 'link-id-2'],
          ['2019-01-01 Beijing BBQ', 'link-id-1'],
          ['2020-06-13 CodingAtHole', 'link-id-4'],
          ['2001-00-00 Something', 'link-id-3'],
          ['2010-12-24 Christmas', 'link-id-2'],
          ['2019-01-01 Beijing BBQ', 'link-id-1'],
          ['2020-06-13 CodingAtHole', 'link-id-4'],
        ],
      },
      {
        name: 'England',
        events: [
          ['2019-01-01 Beijing BBQ', 'link-id-1'],
          ['2020-06-13 CodingAtHole', 'link-id-4'],
        ],
      },
      {
        name: 'France',
        events: [
          ['2019-01-01 Beijing BBQ', 'link-id-1'],
          ['2020-06-13 CodingAtHole', 'link-id-4'],
        ],
      },
      {
        name: 'Germany',
        events: [
          ['2019-01-01 Beijing BBQ', 'link-id-1'],
          ['2020-06-13 CodingAtHole', 'link-id-4'],
        ],
      },
      {
        name: 'Greece',
        events: [
          ['2019-01-01 Beijing BBQ', 'link-id-1'],
          ['2020-06-13 CodingAtHole', 'link-id-4'],
        ],
      },
      {
        name: 'Ireland',
        events: [
          ['2019-01-01 Beijing BBQ', 'link-id-1'],
          ['2020-06-13 CodingAtHole', 'link-id-4'],
        ],
      },
      {
        name: 'Italy',
        events: [
          ['2019-01-01 Beijing BBQ', 'link-id-1'],
          ['2020-06-13 CodingAtHole', 'link-id-4'],
        ],
      },
      {
        name: 'Netherlands',
        events: [
          ['2019-01-01 Beijing BBQ', 'link-id-1'],
          ['2020-06-13 CodingAtHole', 'link-id-4'],
        ],
      },
      {
        name: 'Poland',
        events: [
          ['2019-01-01 Beijing BBQ', 'link-id-1'],
          ['2020-06-13 CodingAtHole', 'link-id-4'],
        ],
      },
      {
        name: 'Romania',
        events: [
          ['2019-01-01 Beijing BBQ', 'link-id-1'],
          ['2020-06-13 CodingAtHole', 'link-id-4'],
        ],
      },
      {
        name: 'Serbia',
        events: [
          ['2019-01-01 Beijing BBQ', 'link-id-1'],
          ['2020-06-13 CodingAtHole', 'link-id-4'],
        ],
      },
    ],
    menu: [
      {name: 'China'},
    ],
  })
}
</script>
