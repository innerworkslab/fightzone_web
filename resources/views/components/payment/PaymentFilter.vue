<script setup>
import { Button } from "@/components/ui/button";
import { useRouter } from "vue-router";
import { RouteNames } from "@/config/route.config";

const router = useRouter();

function addNew() {
    router.push({ name: RouteNames.AddPayment })
}
</script>

<template>
    <div class="flex items-center justify-end gap-4 rounded-lg p-4">
        <Button @click="addNew" variant="default" class="btn-primary"> Add Payment Method </Button>
    </div>
</template>
