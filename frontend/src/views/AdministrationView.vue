<template>
  <b-row>
    <b-col md="6">
      <b-card title="Lista de productos">
        <b-table striped hover :items="items" :fields="fields">
          <template #cell(action)="row">
            <b-button type="button" @click="edit(row.item)">
              Editar
            </b-button>
          </template>
        </b-table>
        <b-pagination v-model="page" :total-rows="totalCount" :per-page="perPage" @update:modelValue="changePage" />
      </b-card>
    </b-col>
    <b-col md="6">
      <b-card title="Productos">
        <b-form @submit="save" @reset="onReset">
          <b-form-group label="Nombre">
            <b-form-input v-model="form.name" type="text" placeholder="Ingrese el nombre" required>
            </b-form-input>
          </b-form-group>
          <b-form-group label="Precio">
            <b-form-input v-model="form.price" type="text" placeholder="Ingrese precio" required>
            </b-form-input>
          </b-form-group>

          <b-button type="submit" variant="primary">
            <span v-if="form.id">
              Actualizar
            </span>
            <span v-else>
              Guardar
            </span>
          </b-button>
          <b-button type="reset" variant="danger">Borrar</b-button>
        </b-form>
      </b-card>
    </b-col>
  </b-row>
</template>

<script>
import service from '../services/products';
export default {
  data() {
    return {
      page: 1,
      totalCount: 0,
      perPage: 5,
      items: [],
      form: {
        id: null,
        name: '',
        price: ''
      },
      fields: [
        { key: 'id', label: '#' },
        { key: 'name', label: 'Nombre' },
        { key: 'price', label: 'Precio' },
        { key: 'action', label: '' },
      ],
    }
  },
  methods: {
    onSubmit(event) {
      event.preventDefault()
      alert(JSON.stringify(this.form))
    },
    onReset(event) {
      event ? event.preventDefault() : null;
      this.form.id = null;
      this.form.name = '';
      this.form.price = '';
    },
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
    validate() {
      const { name, price } = this.form;
      if (!name || !price) {
        return false;
      }

      return true;
    },
    save() {
      if (!this.validate()) {
        alert('Llene todos los campos');
        return;
      }

      if (this.form.id) {
        service.update(this.form.id, this.form)
          .then(
            () => {
              this.get();
              this.onReset();
            }
          )
          .catch(
            (e) => {
              console.log(e);
            }
          );

        return;
      }

      service.create(this.form)
        .then(
          () => {
            this.get();
            this.onReset();
          }
        )
        .catch(
          (e) => {
            console.log(e);
          }
        );
    },
    edit(item) {
      const { id, name, price } = item;
      this.form.id = id;
      this.form.name = name;
      this.form.price = price;
    },
    changePage(value) {
      this.page = value;
      this.get();
    },

  },
  mounted() {
    this.get();
  }
}
</script>