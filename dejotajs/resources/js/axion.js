    import axios from 'axios';

    const axiosInstance = axios.create({
        baseURL: import.meta.env.VITE_API_URL, 
        withCredentials: true, 
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        },
    });

    export default axiosInstance;
