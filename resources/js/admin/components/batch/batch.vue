<template>

<v-layout row ma-3>

  <v-flex xs0 sm0 md1 lg1 xl1>

  </v-flex>

  <v-flex xs12 sm12 md10 lg10 xl10>

    <v-card class="mt-3" :elevation="5">
    <v-card-title>
      Batch
      <v-spacer></v-spacer>
      <v-text-field
      v-model="search"
      append-icon="search"
      label="Search"
      single-line
      hide-details
    ></v-text-field>
      <v-btn style="z-index:1" fixed fab bottom right color="accent" dark @click="dialog=true" :elevation="8"><v-icon>mdi-playlist-plus</v-icon></v-btn>
      <v-dialog
        v-model="dialog"
        width="500"
      >
        <v-card>
          <v-card-title

            class="headline accent"
            primary-title
          >
            Create New Batch
          </v-card-title>

          <v-layout row ma-3>
            <v-flex xs12 sm12 md12 lg12 xl12>
                  <v-row class="customActivityForm">
                    <v-col xs12 sm12 md3 lg3 xl3>
                      Name
                    </v-col>
                    <v-col xs12 sm12 md7 lg7 xl7>
                      <v-text-field
                        filled
                        color="accent"
                        v-model="createdBatch.name"
                      ></v-text-field>
                    </v-col>
                  </v-row>
                  <v-row class="customActivityForm">
                    <v-col xs12 sm12 md3 lg3 xl3>
                      Start Date
                    </v-col>
                    <v-col xs12 sm12 md7 lg7 xl7>
                      <v-menu
                      v-model="menu"
                      :close-on-content-click="false"
                      :nudge-right="40"
                      transition="scale-transition"
                      offset-y
                      full-width
                      min-width="290px"
                      >
                        <template v-slot:activator="{ on }">
                          <v-text-field
                          v-model="createdBatch.start_date"
                          prepend-icon="event"
                          readonly
                          v-on="on"
                          ></v-text-field>
                        </template>
                        <v-date-picker v-model="createdBatch.start_date" @input=" menu = false"></v-date-picker>
                      </v-menu>
                    </v-col>
                  </v-row>
                  <v-row class="customActivityForm">
                    <v-col xs12 sm12 md3 lg3 xl3>
                      End Date
                    </v-col>
                    <v-col xs12 sm12 md7 lg7 xl7>
                      <v-menu
                      v-model="menu2"
                      :close-on-content-click="false"
                      :nudge-right="40"
                      transition="scale-transition"
                      offset-y
                      full-width
                      min-width="290px"
                      >
                        <template v-slot:activator="{ on }">
                          <v-text-field
                          v-model="createdBatch.end_date"
                          prepend-icon="event"
                          readonly
                          v-on="on"
                          ></v-text-field>
                        </template>
                        <v-date-picker v-model="createdBatch.end_date" @input=" menu2 = false"></v-date-picker>
                      </v-menu>
                    </v-col>
                  </v-row>
                </v-flex>
          </v-layout>

          <v-divider></v-divider>

          <v-card-actions>
            <div class="flex-grow-1"></div>
            <v-btn
              color="accent"
              text
              @click="createBatch"
            >
              POST
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-card-title>

      <v-data-table
        :headers="headers"
        :items="batch"
        :items-per-page="5"
        :search="search"
      >
      <template v-slot:item.action="{ item }">
        <v-icon color="error" @click="deleteBatch(item)">delete</v-icon>
      </template>

    </v-data-table>

  </v-card>

  </v-flex>

  <v-flex xs12 sm12 md1 lg1 xl1>

  </v-flex>

</v-layout>
</template>
<script>
  export default {
    data () {
      return {
        dialog:false,
        menu:false,
        menu2:false,
        createdBatch:{start_date: new Date().toISOString().substr(0, 10), end_date:new Date().toISOString().substr(0, 10)},
        search:'',
        headers: [
          {
            text: 'Batch',
            align: 'left',
            sortable: false,
            value: 'name',
          },
          {
            text: 'Start Date',
            align: 'left',
            sortable: false,
            value: 'start_date',
          },
          {
            text: 'End Date',
            align: 'left',
            sortable: false,
            value: 'end_date',
          },
          {
            text: 'Actions',
            value: 'action',
            align: 'right',
            sortable: false },
        ],
        batch:[],
      }
    },
    computed:{
      Admin(){
        return this.$store.getters.getAdmin;
      }
    },
    methods: {
      deleteBatch (batch) {
        const index = this.batch.indexOf(batch);
        this.$http.put(this.$root.api + '/batches/delete/'+ batch.id,{
          admin_id: this.Admin.id
        },
        {
          headers: {
            Authorization: 'Bearer ' + this.Admin.token
          }
        }).then((response) =>{
          console.log(response);
          this.batch.splice(index, 1);
        })
      },
      createBatch(){
        this.$http.post(this.$root.api + '/batches',{
          admin_id: this.Admin.id,
          name: this.createdBatch.name,
          start_date: this.createdBatch.start_date,
          end_date: this.createdBatch.end_date
        },
        {
          headers: {
            Authorization: 'Bearer ' + this.Admin.token
          }
        }).then((response) =>{
          console.log(response);
          this.dialog = false;
          console.log('false');
          this.batch.unshift({
            name: this.createdBatch.name,
            start_date: this.createdBatch.start_date,
            end_date: this.createdBatch.end_date
          });

        })
      },
      getbatch(){
        this.$http.get(this.$root.api + '/batches',{
          headers: {
              Authorization: 'Bearer '+ this.Admin.token
          }
        }).then(response=>{
          this.batch= response.body.data;
          console.log(this.batch);
        }, response => {
          console.log('error');
        })
      },
    },
    created(){
      this.getbatch()
    }
  }
</script>
