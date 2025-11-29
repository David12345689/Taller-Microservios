export class ApiService {
    constructor(baseURL) {
        this.baseURL = baseURL;
        this.token = localStorage.getItem('token');
    }

    getHeaders(isJson = true) {
        const headers = {
            'Authorization': 'Bearer ' + this.token
        };
        if (isJson) headers['Content-Type'] = 'application/json';
        return headers;
    }

    async get(endpoint) {
        const res = await fetch(this.baseURL + endpoint, {
            method: 'GET',
            headers: this.getHeaders(false)
        });
        return await res.json();
    }

    async post(endpoint, data) {
        const res = await fetch(this.baseURL + endpoint, {
            method: 'POST',
            headers: this.getHeaders(true),
            body: JSON.stringify(data)
        });
        return await res.json();
    }

    async put(endpoint, data) {
        const res = await fetch(this.baseURL + endpoint, {
            method: 'PUT',
            headers: this.getHeaders(true),
            body: JSON.stringify(data)
        });
        return await res.json();
    }
}