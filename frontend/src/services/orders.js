import http from './http';

class Services {
    search(params) {
        return http.get('/orders', { params });
    }
    create(data) {
        return http.post('/orders', data);
    }
    delete(id) {
        return http.delete(`/orders/${id}`);
    }
    get(id) {
        return http.get(`/orders/${id}`);
    }
    update(id, data) {
        return http.put(`/orders/${id}`, data);
    }
}

export default new Services();