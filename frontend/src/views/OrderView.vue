<template>
  <b-row>
    <b-col md="6">
      <b-card title="Lista de ordenes">
        <b-table striped hover :items="items" :fields="fields">
          <template #cell(action)="row">
            <b-button type="button" @click="loadData(row.item)">
              Ver
            </b-button>
            <b-button type="button" @click="deleteOrder(row.item.id)" variant="danger">
              Borrar
            </b-button>
          </template>
        </b-table>
        <b-pagination v-model="page" :total-rows="totalCount" :per-page="perPage" @update:modelValue="changePage" />
      </b-card>
    </b-col>
    <b-col md="6" v-if="view">
      <b-card title="Orden">
        <h4>
          {{ view.sku }}
        </h4>
        <table class="table">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Nombre</th>
              <th scope="col">Cantidad</th>
              <th scope="col">Precio</th>
              <th scope="col">Total</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, key) in view.order_details" :key="key">
              <th scope="row">
                {{ item.id }}
              </th>
              <td>
                {{ item.name }}
              </td>
              <td>
                {{ item.quantity }}
              </td>
              <td>
                {{ item.price }}
              </td>
              <td>
                {{ item.total }}
              </td>
            </tr>
          </tbody>
          <tfoot>
            <tr>
              <td>Total</td>
              <td colspan="4">
                {{ view.total }}
              </td>
            </tr>
          </tfoot>
        </table>
      </b-card>
    </b-col>
  </b-row>
</template>

<script>
import service from '../services/orders';
export default {
  data() {
    return {
      page: 1,
      totalCount: 0,
      perPage: 5,
      items: [],
      fields: [
        { key: 'id', label: '#' },
        { key: 'sku', label: 'Código' },
        { key: 'date', label: 'Fecha' },
        { key: 'total', label: 'Total' },
        { key: 'action', label: '' },
      ],
      view: null,
    }
  },
  methods: {
    get() {
      const params = {};
      params.page = this.page;
      params.per_page = this.perPage;
      service.search(params)
        .then((res) => {
          const { data: { data, _meta } } = res;
          const { totalCount, pageCount } = _meta;
          this.totalCount = totalCount;
          this.pageCount = pageCount;
          this.items = data;
        })
        .catch((e) => {
          console.log(e);
        })
    },
    changePage(value) {
      this.page = value;
      this.get();
    },
    loadData(item) {
      this.view = item;
    },
    deleteOrder(id) {
      this.view = null;
      service.delete(id)
        .then(
          () => {
            this.get();
          }
        )
        .catch(
          (e) => {
            console.log(e);
          }
        )
    }
  },
  mounted() {
    this.get();
  }
}
</script>
