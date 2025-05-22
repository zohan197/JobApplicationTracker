import { defineStore } from 'pinia';
import axios from 'axios';
import { router } from '@inertiajs/vue3'

export const useJobListingStore = defineStore('jobListing', {
    state: () => ({
        listings: [] as Array<any>,
        loading: false,
    }),

    actions: {
        async createListing(data: any) {
            try {
                this.loading = true;
                const response = await axios.post('/job-listings', data);
                await router.reload({ only: ['listings'] }); // reload just the listings prop
                return response.data;
            } catch (error: any) {
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async deleteListing(id: number) {
            try {
                this.loading = true;
                await axios.delete(`/job-listings/${id}`);
                await router.reload({ only: ['listings'] });
            } catch (error: any) {
                console.error('Failed to delete job listing:', error);
            } finally {
                this.loading = false;
            }
        },

        async updateListing(id: number, data: any) {
            try {
                this.loading = true;
                await axios.put(`/job-listings/${id}`, data);
                await router.reload({ only: ['listings'] });
            } catch (error: any) {
                console.error('Failed to update job listing:', error);
            } finally {
                this.loading = false;
            }
        },

    },
});
