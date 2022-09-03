<template>
  <div class="container">
    <div>
      <div class="grid-container">
        <div v-for="(item, key) in items" :key="key">
          <h3>
            {{ item.name }}
          </h3>
          <p>
            {{ item.price }}
          </p>
          <b-button variant="primary" @click="addProduct(item)">
            Agregar
          </b-button>
        </div>
      </div>
      <b-pagination v-model="page" :total-rows="totalCount" :per-page="perPage" @update:modelValue="changePage" />
      <b-button variant="outline-success" :disabled="!cart.length" @click="processOrder">
        Procesar orden {{ cart.length }}
      </b-button>
    </div>
  </div>
</template>
<script>
import service from '../services/products';
import orderService from '../services/orders';
import getLocalStorage from '../utils/getLocalStorage';
import setLocalStorage from '../utils/setLocalStorage';

export default {
  data() {
    return {
      page: 1,
      totalCount: 0,
      perPage: 4,
      items: [],
      cart: [],
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
    addProduct(item) {
      let flag = 0;
      if (this.cart.length) {
        this.cart.forEach((cartItem, index, cartArray) => {
          if (cartItem.product_id === item.id) {
            cartArray[index].quantity++;
            cartArray[index].calculatedPrice = cartArray[index].quantity * cartArray[index].price;
            flag = 1;
          }
        });
      }

      if (flag === 0) {
        const { id, name, price } = item;

        this.cart.push({
          product_id: id,
          name,
          quantity: 1,
          price,
          calculatedPrice: price,
        });
      }

      setLocalStorage('cart', this.cart);
    },
    processOrder() {
      const data = {
        order_details: this.cart
      }
      orderService.create(data)
        .then(
          () => {

          }
        )
        .catch(
          (e) => {
            console.log(e);
          }
        );
      localStorage.removeItem('cart');
      this.$router.push({
        name: 'order',
      });
    }
  },
  mounted() {
    this.get();
    const cart = getLocalStorage('cart');
    this.cart = cart ? cart : [];
  }
}
</script>
<style>
.container {
  display: flex;
  justify-content: center;
}

.grid-container {
  width: 750px;
  display: grid;
  grid-template-columns: 1fr 1fr 1fr 1fr;
  grid-gap: 5px;
  padding: 5px;
}

.grid-container>div {
  background-color: rgba(255, 255, 255, 0.8);
  text-align: center;
  padding: 20px 0;
  font-size: 30px;
  border: solid 1px black;
  border-radius: 15px;
}
</style>