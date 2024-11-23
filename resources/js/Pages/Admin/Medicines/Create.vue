<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Add Medicine</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-end m-2 p-2">
          <Link href="/admin/medicines/index" class="px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white rounded">Back</Link>
        </div>

        <!-- Enhanced notification for existing medicine -->
        <div v-if="isExisting" class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-4 mx-4">
          <p class="font-semibold">This medicine already exists in the inventory:</p>
          <ul class="list-disc list-inside ml-4 mt-2">
            <li>Current total stock: {{ totalExistingQuantity }} units</li>
            <li>Adding {{ form.quantity || 0 }} more units</li>
            <li>New total will be: {{ (parseInt(form.quantity) || 0) + parseInt(totalExistingQuantity) }} units</li>
            <li>Current expiration date: {{ formatDate(currentExpDate) }}</li>
            <li class="font-semibold">Expiration date will be updated to: {{ formatDate(form.expdate) }}</li>
          </ul>
        </div>

        <div class="flex justify-center">
          <div class="bg-white shadow-lg rounded-lg p-6 max-w-md w-full">
            <form @submit.prevent="storeMedicine">
              <div class="relative z-0 w-full mb-5 group">
                <input v-model="form.name" type="text" name="name" id="name" 
                  class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" 
                  placeholder=" " 
                  required 
                />
                <label for="name" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Name</label>
              </div>

              <!-- Price fields -->
              <div class="grid md:grid-cols-3 md:gap-3">
                <div class="relative z-0 w-full mb-1 group">
                  <input v-model="form.lprice" type="number" name="lprice" id="lprice" 
                    class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" 
                    placeholder=" " 
                    required 
                    :readonly="isExisting"
                  />
                  <label for="lprice" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Lowest Price</label>
                </div>
                <div class="relative z-0 w-full mb-1 group">
                  <input v-model="form.mprice" type="number" name="mprice" id="mprice" 
                    class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" 
                    placeholder=" " 
                    required 
                    :readonly="isExisting"
                  />
                  <label for="mprice" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Median Price</label>
                </div>
                <div class="relative z-0 w-full mb-5 group">
                  <input v-model="form.hprice" type="number" name="hprice" id="hprice" 
                    class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" 
                    placeholder=" " 
                    required 
                    :readonly="isExisting"
                  />
                  <label for="hprice" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Highest Price</label>
                </div>
              </div>

              <!-- Other fields -->
              <div class="grid md:grid-cols-3 md:gap-6">
                <div class="relative z-0 w-full mb-5 group">
                  <input v-model="form.quantity" type="number" name="quantity" id="quantity" 
                    class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" 
                    placeholder=" " 
                    required 
                  />
                  <label for="quantity" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Quantity</label>
                </div>
                <div class="relative z-0 w-full mb-5 group">
                  <input v-model="form.dosage" type="text" name="dosage" id="dosage" 
                    class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" 
                    placeholder=" " 
                    required 
                    :readonly="isExisting"
                  />
                  <label for="dosage" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Dosage</label>
                </div>
                <div class="relative z-0 w-full mb-5 group">
                  <input v-model="form.expdate" type="date" name="expdate" id="expdate" 
                    class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" 
                    placeholder=" " 
                    required 
                  />
                  <label for="expdate" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Expiration Date</label>
                </div>
              </div>

              <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Add</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AdminAuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { ref, watch } from 'vue';

const form = useForm({
  name: null,
  lprice: null,
  mprice: null,
  hprice: null,
  quantity: null,
  dosage: null,
  expdate: null,
  existing_id: null,
});

const isExisting = ref(false);
const totalExistingQuantity = ref(0);
const currentExpDate = ref(null);

// Enhanced watch function for medicine name
watch(() => form.name, async (newValue) => {
  if (newValue && newValue.length > 2) {
    try {
      const response = await axios.get(`/api/medicines/check/${newValue}`);
      if (response.data.exists) {
        isExisting.value = true;
        const medicine = response.data.medicine;
        totalExistingQuantity.value = medicine.quantity;
        currentExpDate.value = medicine.expdate;
        
        // Auto-fill all existing medicine details
        form.lprice = medicine.lprice;
        form.mprice = medicine.mprice;
        form.hprice = medicine.hprice;
        form.dosage = medicine.dosage;
        form.existing_id = medicine.id;

        // Make price and dosage fields readonly
        document.getElementById('lprice').readOnly = true;
        document.getElementById('mprice').readOnly = true;
        document.getElementById('hprice').readOnly = true;
        document.getElementById('dosage').readOnly = true;
      } else {
        // Reset form and make fields editable if medicine doesn't exist
        isExisting.value = false;
        totalExistingQuantity.value = 0;
        currentExpDate.value = null;
        form.existing_id = null;
        form.lprice = null;
        form.mprice = null;
        form.hprice = null;
        form.dosage = null;

        // Make fields editable again
        document.getElementById('lprice').readOnly = false;
        document.getElementById('mprice').readOnly = false;
        document.getElementById('hprice').readOnly = false;
        document.getElementById('dosage').readOnly = false;
      }
    } catch (error) {
      console.error('Error checking medicine:', error);
    }
  } else {
    // Reset everything if name is too short
    isExisting.value = false;
    totalExistingQuantity.value = 0;
    currentExpDate.value = null;
    form.existing_id = null;
    form.lprice = null;
    form.mprice = null;
    form.hprice = null;
    form.dosage = null;

    // Make fields editable
    document.getElementById('lprice').readOnly = false;
    document.getElementById('mprice').readOnly = false;
    document.getElementById('hprice').readOnly = false;
    document.getElementById('dosage').readOnly = false;
  }
});

function formatDate(date) {
  if (!date) return '';
  return new Date(date).toLocaleDateString();
}

function storeMedicine() {
  form.post('/admin/medicines/', {
    onSuccess: () => {
      form.reset();
      isExisting.value = false;
      totalExistingQuantity.value = 0;
      currentExpDate.value = null;
    }
  });
}
</script>
  