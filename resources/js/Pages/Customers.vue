<template>
    <div class="customer-list-container">
        <!-- Alphabet Bar -->
        <div class="alphabet-bar">
            <button
                v-for="letter in alphabet"
                :key="letter"
                class="alphabet-button"
                :class="{ 'active-letter': letter === activeLetter }"
                @click="scrollToLetter(letter)"
            >
                {{ letter }}
            </button>
        </div>

        <!-- Customer  List -->
        <div class="customer-list" ref="customerList" @scroll="handleScroll">
            <div
                v-for="(customerGroup, letter) in visibleCustomers"
                :key="letter"
                :ref="(el) => setDynamicRef(el, letter)"
                class="customer-section"
            >
                <h2>{{ letter }}</h2>
                <ul>
                    <li v-for="customer in customerGroup" :key="customer.id">
                        {{ customer.name }}
                    </li>
                </ul>
                <div v-if="!customerGroup.length" class="no-customers">
                    No customer available for this letter.
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from "vue";

// Props
const props = defineProps({
    customers: {
        type: Array,
        required: true,
    },
});

// Refs and reactive state
const customerList = ref(null);
const visibleCustomers = reactive({});
const loading = reactive({});
const currentPage = reactive({});
const sectionRefs = ref({});
const alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ".split("");
const activeLetter = ref(null);
const itemsPerPage = 20;

// Initialize customer groups and page states
const initializeCustomerGroups = () => {
    alphabet.forEach((letter) => {
        visibleCustomers[letter] = [];
        currentPage[letter] = 0;
        loading[letter] = false;
    });
};

// Group customers by their starting letter
const groupCustomersByLetter = () => {
    const groupedCustomers = {};
    alphabet.forEach((letter) => {
        groupedCustomers[letter] = props.customers.filter(
            (customer) => customer.name.charAt(0).toUpperCase() === letter
        );
    });
    return groupedCustomers;
};

// Load customers for a specific letter
const loadCustomers = (letter) => {
    if (loading[letter]) return;

    loading[letter] = true;
    const groupedCustomers = groupCustomersByLetter();
    const startIndex = currentPage[letter] * itemsPerPage;
    const customersForLetter = groupedCustomers[letter]?.slice(
        startIndex,
        startIndex + itemsPerPage
    );

    if (customersForLetter?.length) {
        visibleCustomers[letter].push(...customersForLetter);
        currentPage[letter]++;
    }
    loading[letter] = false;
};

// Handle scroll events to load more customers when scrolling to the bottom
const handleScroll = () => {
    Object.entries(sectionRefs.value).forEach(([letter, section]) => {
        if (isSectionScrolledToBottom(section)) {
            loadCustomers(letter);
        }
    });
};

// Check if a section is scrolled to the bottom
const isSectionScrolledToBottom = (section) =>
    section &&
    section.scrollHeight === section.scrollTop + section.clientHeight;

// Scroll to the section of a specific letter
const scrollToLetter = (letter) => {
    const targetSection = sectionRefs.value[letter];
    const listContainer = customerList.value;

    const offsetTop = targetSection.offsetTop;
    const offsetHeight = targetSection.offsetHeight;

    // Add some padding to make sure the content is well within view
    const offset = 20; // Adjust this value based on your needs

    // Scroll the container to the correct position
    customerList.value.scrollTo({
        top: offsetTop - offset, // Adjust with offset
        behavior: "smooth",
    });

    if (!visibleCustomers[letter]?.length) {
        loadCustomers(letter);
    }
    activeLetter.value = letter;
};

// Set a dynamic ref for each section (group of customers by letter)
const setDynamicRef = (el, letter) => {
    if (el) {
        sectionRefs.value[letter] = el;
    }
};

// Initialize customer data when the component mounts
onMounted(() => {
    initializeCustomerGroups();
    activeLetter.value = "A";
    alphabet.forEach((letter) => loadCustomers(letter));
});
</script>

<style scoped>
.customer-list-container {
    display: flex;
    flex-direction: row;
    height: 100vh;
}

.alphabet-bar {
    position: fixed;
    right: 0;
    top: 0;
    width: 50px;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background-color: #f4f4f4;
}

.alphabet-button {
    margin: 5px 0;
    background: #fff;
    border: 1px solid #ccc;
    padding: 5px;
    cursor: pointer;
    font-size: 14px;
}

.customer-list {
    flex: 1;
    overflow-y: scroll;
    padding-right: 60px;
    height: 100vh;
}

.customer-section {
    padding: 20px;
    border-bottom: 1px solid #ddd;
}

.customer-section h2 {
    font-size: 20px;
    margin-bottom: 10px;
}

.no-customers {
    text-align: center;
    padding: 1rem;
    font-style: italic;
}
.active-letter {
    background-color: #ff0000;
    border-color: #ff0000;
    color: #fff;
    font-weight: 600;
}
</style>
