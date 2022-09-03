import http from './http';

class Services {
    search(params) {
        return http.get('/products', { params });
    }
    create(data) {
        return http.post('/products', data);
    }
    delete(id) {
        return http.delete(`/products/${id}`);
    }
    get(id) {
        return http.get(`/products/${id}`);
    }
    update(id, data) {
        return http.put(`/products/${id}`, data);
    }
}

export default new Services();