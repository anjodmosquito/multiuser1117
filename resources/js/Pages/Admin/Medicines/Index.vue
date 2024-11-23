<template>
    <Head title="Medicines" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Medicine Index</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Add search bar and Add Medicines button in a flex container -->
                <div class="flex justify-between items-center mb-6">
                    <!-- Search Bar -->
                    <div class="w-1/2">
                        <div class="relative">
                            <input v-model="searchQuery" type="text" placeholder="Search medicines by name or dosage..."
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:border-gray-600" />
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <!-- Add Medicines Button -->
                    <div class="flex space-x-2">
                        <Link :href="route('admin.medicines.create')"
                            class="px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white rounded">
                            Add Medicines
                        </Link>
                        <Link :href="route('admin.medicines.history')"
                            class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded flex items-center">
                            <span class="mr-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </span>
                            History
                        </Link>
                        <button 
                            @click="generateDetailedReport"
                            class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded flex items-center"
                        >
                            <span class="mr-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </span>
                            Download Report
                        </button>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Name</th>
                                    <th scope="col" class="px-6 py-3">Low Price</th>
                                    <th scope="col" class="px-6 py-3">Median Price</th>
                                    <th scope="col" class="px-6 py-3">Highest Price</th>
                                    <th scope="col" class="px-6 py-3">Quantity</th>
                                    <th scope="col" class="px-6 py-3">Dosage</th>
                                    <th scope="col" class="px-6 py-3">Exp Date</th>
                                    <th scope="col" class="px-6 py-3">Status</th>
                                    <th scope="col" class="px-6 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!medicines || medicines.length === 0">
                                    <td colspan="9" class="px-6 py-4 text-center">No medicines found</td>
                                </tr>
                                <tr v-else v-for="medicine in medicines" :key="medicine.id"
                                    class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ medicine.name }}
                                    </td>
                                    <td class="px-6 py-4">₱{{ medicine.lprice }}</td>
                                    <td class="px-6 py-4">₱{{ medicine.mprice }}</td>
                                    <td class="px-6 py-4">₱{{ medicine.hprice }}</td>
                                    <td class="px-6 py-4">{{ medicine.quantity }}</td>
                                    <td class="px-6 py-4">{{ medicine.dosage }}</td>
                                    <td class="px-6 py-4">
                                        <span :class="{'text-red-600': isExpired(medicine.expdate)}">
                                            {{ formatDate(medicine.expdate) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span :class="medicine.status === 'disabled' ? 'text-red-600' : 'text-green-600'">
                                            {{ medicine.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 space-x-2">
                                        <Link v-if="medicine.status !== 'disabled'"
                                            :href="route('admin.medicines.edit', medicine.id)"
                                            class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded">
                                            Edit
                                        </Link>
                                        <button @click="confirmDelete(medicine.id)"
                                            class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AdminAuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import Swal from 'sweetalert2';
import jsPDF from 'jspdf';
import 'jspdf-autotable';
import { format } from 'date-fns';

const props = defineProps({
    medicines: {
        type: Array,
        default: () => []
    }
});

const searchQuery = ref('');

function formatDate(date) {
    return format(new Date(date), 'yyyy-MM-dd');
}

function isExpired(date) {
    return new Date(date) < new Date();
}

function confirmDelete(medicineId) {
    if (!medicineId) return;

    Swal.fire({
        title: 'Are you sure?',
        text: 'You will not be able to recover this medicine!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel!',
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('admin.medicines.destroy', medicineId));
        }
    });
}

const performSearch = debounce((query) => {
    router.get(route('admin.medicines.index'),
        { search: query },
        { preserveState: true, preserveScroll: true }
    );
}, 300);

watch(searchQuery, (newValue) => {
    performSearch(newValue);
});

function generateDetailedReport() {
    const doc = new jsPDF();
    
    doc.setFontSize(18);
    doc.text('Medicine Inventory Report', 14, 20);
    
    doc.setFontSize(12);
    doc.text(`Generated on: ${format(new Date(), 'yyyy-MM-dd HH:mm')}`, 14, 30);
    
    const tableColumn = [
        "Name",
        "Dosage",
        "Quantity",
        "Prices (L/M/H)",
        "Exp Date",
        "Status"
    ];
    
    const tableRows = props.medicines.map(medicine => [
        medicine.name,
        medicine.dosage,
        medicine.quantity,
        `₱${medicine.lprice}/${medicine.mprice}/${medicine.hprice}`,
        formatDate(medicine.expdate),
        medicine.status
    ]);

    doc.autoTable({
        head: [tableColumn],
        body: tableRows,
        startY: 40,
        styles: { fontSize: 10, cellPadding: 3 },
        headStyles: { fillColor: [41, 128, 185] },
        didDrawPage: function(data) {
            doc.setFontSize(10);
            doc.text(
                `Total Medicines: ${props.medicines.length}`,
                14,
                doc.internal.pageSize.height - 10
            );
        }
    });

    doc.save(`medicine-inventory-report-${format(new Date(), 'yyyy-MM-dd')}.pdf`);
}
</script>
