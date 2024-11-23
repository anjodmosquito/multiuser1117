<template>
    <Head title="Medicine History" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Medicine History</h2>
                <Link 
                    :href="route('admin.medicines.index')"
                    class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded"
                >
                    Back to Medicines
                </Link>
            </div>
        </template>

        <div v-if="$page.props.error" 
            class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-6">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ $page.props.error }}</span>
            </div>
        </div>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Date Filter -->
                <div class="mb-6 bg-white p-4 rounded-lg shadow flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <label class="text-sm font-medium text-gray-700">Start Date:</label>
                        <input 
                            type="date" 
                            v-model="filters.start_date"
                            class="rounded border-gray-300"
                        >
                    </div>
                    <div class="flex items-center space-x-2">
                        <label class="text-sm font-medium text-gray-700">End Date:</label>
                        <input 
                            type="date" 
                            v-model="filters.end_date"
                            class="rounded border-gray-300"
                        >
                    </div>
                    <button 
                        @click="applyFilters"
                        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
                    >
                        Apply Filter
                    </button>
                    <button 
                        @click="generateHistoryReport"
                        class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 flex items-center"
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

                <!-- History Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Medicine</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prices (L/M/H)</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity Added</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Quantity</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dosage</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Exp Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-if="!histories?.data || histories.data.length === 0">
                                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                        No history records found
                                    </td>
                                </tr>
                                <tr v-else v-for="history in histories.data" :key="history.id" 
                                    class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ formatDateTime(history.created_at) }}
                                    </td>
                                    <td class="px-6 py-4">{{ history.name }}</td>
                                    <td class="px-6 py-4">
                                        ₱{{ history.lprice }}/{{ history.mprice }}/{{ history.hprice }}
                                    </td>
                                    <td class="px-6 py-4">{{ history.quantity_added }}</td>
                                    <td class="px-6 py-4">{{ history.total_quantity }}</td>
                                    <td class="px-6 py-4">{{ history.dosage }}</td>
                                    <td class="px-6 py-4">
                                        <span :class="{'text-red-600': isExpired(history.expdate)}">
                                            {{ formatDate(history.expdate) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span :class="{
                                            'bg-green-100 text-green-800': history.action_type === 'add',
                                            'bg-blue-100 text-blue-800': history.action_type === 'update'
                                        }" class="px-2 py-1 rounded-full text-xs font-medium">
                                            {{ history.action_type === 'add' ? 'New Medicine' : 'Stock Update' }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Add this after the table -->
                <div v-if="histories?.data?.length > 0" class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <Link v-if="histories.prev_page_url"
                                :href="histories.prev_page_url"
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                            >
                                Previous
                            </Link>
                            <Link v-if="histories.next_page_url"
                                :href="histories.next_page_url"
                                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                            >
                                Next
                            </Link>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Showing
                                    <span class="font-medium">{{ histories.from }}</span>
                                    to
                                    <span class="font-medium">{{ histories.to }}</span>
                                    of
                                    <span class="font-medium">{{ histories.total }}</span>
                                    results
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                    <Link v-for="(link, index) in histories.links"
                                        :key="index"
                                        :href="link.url"
                                        :class="[
                                            'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                            {
                                                'bg-blue-50 border-blue-500 text-blue-600': link.active,
                                                'bg-white border-gray-300 text-gray-500 hover:bg-gray-50': !link.active,
                                                'rounded-l-md': index === 0,
                                                'rounded-r-md': index === histories.links.length - 1
                                            }
                                        ]"
                                        v-html="link.label"
                                    />
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AdminAuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { format } from 'date-fns';
import jsPDF from 'jspdf';
import 'jspdf-autotable';

const props = defineProps({
    histories: {
        type: Object,
        required: true
    },
    filters: {
        type: Object,
        default: () => ({
            start_date: '',
            end_date: ''
        })
    }
});

function formatDate(date) {
    return format(new Date(date), 'yyyy-MM-dd');
}

function formatDateTime(datetime) {
    return format(new Date(datetime), 'yyyy-MM-dd HH:mm');
}

function isExpired(date) {
    return new Date(date) < new Date();
}

function applyFilters() {
    router.get(route('admin.medicines.history'), {
        start_date: props.filters.start_date,
        end_date: props.filters.end_date
    }, {
        preserveState: true
    });
}

function generateHistoryReport() {
    const doc = new jsPDF();
    
    doc.setFontSize(18);
    doc.text('Medicine History Report', 14, 20);
    
    doc.setFontSize(12);
    doc.text(`Generated on: ${formatDateTime(new Date())}`, 14, 30);
    
    if (props.filters.start_date && props.filters.end_date) {
        doc.text(`Date Range: ${props.filters.start_date} to ${props.filters.end_date}`, 14, 40);
    }
    
    const tableColumn = [
        "Date",
        "Medicine",
        "Prices (L/M/H)",
        "Qty Added",
        "Total Qty",
        "Dosage",
        "Exp Date",
        "Action"
    ];
    
    const tableRows = props.histories.data.map(history => [
        formatDateTime(history.created_at),
        history.name,
        `₱${history.lprice}/${history.mprice}/${history.hprice}`,
        history.quantity_added,
        history.total_quantity,
        history.dosage,
        formatDate(history.expdate),
        history.action_type === 'add' ? 'New Medicine' : 'Stock Update'
    ]);

    doc.autoTable({
        head: [tableColumn],
        body: tableRows,
        startY: props.filters.start_date ? 45 : 40,
        styles: { fontSize: 8, cellPadding: 2 },
        headStyles: { fillColor: [41, 128, 185] },
    });

    doc.save(`medicine-history-report-${format(new Date(), 'yyyy-MM-dd-HHmm')}.pdf`);
}
</script> 