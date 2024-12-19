<template>
    <div class="customer-list-container">
        <!-- Alphabet Bar -->
        <AlphabetBar
            :alphabet="alphabet"
            :active-letter="activeLetter"
            @letter-click="handleLetterClick"
        />

        <!-- Customer List -->
        <div class="customer-list" ref="customerList">
            <!-- Loading Indicator -->
            <LoadingIndicator v-if="isLoading" />

            <!-- Customer Sections -->
            <div
                v-for="letter in alphabet"
                :key="letter"
                :ref="setSectionRef(letter)"
                class="customer-section"
                :class="{
                    empty:
                        !visibleCustomers[letter] ||
                        visibleCustomers[letter].length === 0,
                }"
            >
                <h2>{{ letter }}</h2>
                <ul>
                    <li
                        v-for="customer in visibleCustomers[letter]"
                        :key="customer.id"
                    >
                        {{ customer.name }}
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted, onBeforeUnmount, nextTick } from "vue";
import axios from "axios";
import AlphabetBar from "./../Components/AlphabetBar.vue";
import LoadingIndicator from "./../Components/LoadingIndicator.vue";

// Reactive state
const customerList = ref(null);
const visibleCustomers = reactive({});
const alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ".split(""); // A-Z letters
const isLoading = ref(false);
const sectionRefs = reactive({});
const letterPages = reactive({});
const hasFetchedLetter = reactive({}); // Track if data is fetched for each letter
const activeLetter = ref("A");

// Group customers by their first letter
const groupCustomersByLetter = (customers) => {
    customers.forEach((customer) => {
        const firstLetter = customer.name.charAt(0).toUpperCase();
        if (!visibleCustomers[firstLetter]) {
            visibleCustomers[firstLetter] = [];
        }
        visibleCustomers[firstLetter].push(customer);
    });
};

// Fetch customers for a specific letter
const fetchCustomersForLetter = async (letter) => {
    if (isLoading.value || hasFetchedLetter[letter]) return; // Prevent if already fetched

    if (!letterPages[letter]) {
        letterPages[letter] = 1;
    }

    isLoading.value = true;
    hasFetchedLetter[letter] = true;

    try {
        const response = await axios.get(
            `/customers/load-more?letter=${letter}`
        );
        if (response.data.length) {
            groupCustomersByLetter(response.data);
        } else {
            hasFetchedLetter[letter] = true; // No data for this letter
        }
    } catch (error) {
        console.error("Error fetching customers:", error);
    } finally {
        isLoading.value = false;
    }
};

// Handle the letter click
const handleLetterClick = async (letter) => {
    scrollToLetter(letter);

    if (!visibleCustomers[letter] || visibleCustomers[letter].length === 0) {
        await fetchCustomersForLetter(letter);
    }
};

// Scroll to the corresponding letter section
const scrollToLetter = (letter) => {
    // Use nextTick to delay scroll until after DOM is updated
    nextTick(() => {
        const target = sectionRefs[letter];
        if (target) {
            target.scrollIntoView({
                behavior: "auto", // Change to 'auto' for instant scroll
                block: "start", // Align it to the top
                inline: "nearest",
            });

            activeLetter.value = letter;
        } else {
            console.error("No target found for letter:", letter);
        }
    });
};

// Setup intersection observer for each letter section
const setupIntersectionObserver = (letter) => {
    const observer = new IntersectionObserver(
        async (entries) => {
            for (const entry of entries) {
                if (entry.isIntersecting && !hasFetchedLetter[letter]) {
                    await fetchCustomersForLetter(letter);
                }
            }
        },
        { root: customerList.value, rootMargin: "0px", threshold: 0.5 } // 50% of the section in view
    );
    if (sectionRefs[letter]) {
        observer.observe(sectionRefs[letter]);
    }
};

// Dynamically set ref for each letter section and observe it
const setSectionRef = (letter) => {
    return (el) => {
        if (el) {
            sectionRefs[letter] = el;
            setupIntersectionObserver(letter);
        }
    };
};

// Initial load for 'A' or any other letter
onMounted(async () => {
    await fetchCustomersForLetter("A"); // Fetch for 'A' initially
});

// Cleanup the observers when the component is destroyed
onBeforeUnmount(() => {
    Object.values(sectionRefs).forEach((el) => {
        const observer = new IntersectionObserver();
        observer.disconnect(); // Disconnect all observers
    });
});
</script>

<style scoped>
.customer-list {
    overflow-y: auto;
    height: 500px;
}

.customer-section {
    padding: 20px;
    border-bottom: 1px solid #ddd;
}

.customer-section h2 {
    font-size: 20px;
    margin-bottom: 10px;
}

.empty {
    display: block; /* Make sure the empty sections are still rendered and observed */
}
</style>
