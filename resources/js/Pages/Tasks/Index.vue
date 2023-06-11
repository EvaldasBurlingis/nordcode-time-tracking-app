<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, router} from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import {computed, ref, watch} from 'vue';
import fileDownload from '@/Helpers/fileDownload.js';
import {FileTypes} from '@/Enums/FileTypes.js';
import {useForm} from '@inertiajs/vue3';
import {ElNotification} from 'element-plus';
import {
Download
} from '@element-plus/icons-vue'


const props = defineProps(['tasks', 'dateFrom', 'dateTo'])

const allowDownload = ref(false);
const dateRange = ref([props.dateFrom, props.dateTo]);
const isCreateTaskDialogVisible = ref(false);
const form = useForm({
    title: '',
    comment: '',
    date: '',
    time_spent: null
})

const onSearch = () => {
    if (dateRange.value && dateRange.value[0] && dateRange.value[1]) {
        router.get(route('tasks.index'), {
            dateFrom: dateRange.value[0],
            dateTo: dateRange.value[1]
        }, {
            preserveState: true,
            replace: true,
            onSuccess: (page) => {
                if (page.props.tasks.data.length) {
                    allowDownload.value = true
                }
            }
        })
    } else {
        allowDownload.value = false
        router.get(route('tasks.index'), {}, {preserveState: true, replace: true})
    }
}

const onDownload = (type) => {
    axios.post(route('tasks.download'), {
        dateFrom: dateRange.value[0],
        dateTo: dateRange.value[1],
        fileType: type
    }, {
        responseType: 'blob'
    })
        .then(response => {
            const filename = response.headers['content-disposition'].split('=')[1]
            fileDownload(response.data, filename)
        }).catch(error => {
        const parsedErrorResponse = JSON.parse(error.request.response)

        if ('message' in parsedErrorResponse) {
            ElNotification({
                title: 'Error',
                message: parsedErrorResponse.message,
                type: 'error',
                position: 'bottom-right',
            })
        } else {
            ElNotification({
                title: 'Error',
                message: 'Something went wrong',
                type: 'error',
                position: 'bottom-right',
            })
        }
    })
}

const onCreate = () => {
    form.post(route('tasks.store'), {
        onSuccess: () => {
            form.reset('title', 'comment', 'date', 'time_spent')

            isCreateTaskDialogVisible.value = false
            ElNotification({
                title: 'Task created successfully',
                type: 'success',
                position: 'bottom-right',
            })
        },
    })
}

watch(dateRange, (newValue) => {
    if (!newValue) {
        onSearch()
    }
}, {deep: true})

</script>

<template>
    <Head title="Tasks"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tasks</h2>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white rounded-md shadow overflow-x-auto p-4">
                    <div class="flex justify-between mb-4">
                        <div class="flex align-center">
                            <div class="mr-3">
                                <el-date-picker
                                    v-model="dateRange"
                                    type="daterange"
                                    range-separator="To"
                                    start-placeholder="Start date"
                                    end-placeholder="End date"
                                    format="YYYY-MM-DD"
                                    value-format="YYYY-MM-DD"
                                />
                            </div>
                            <div class="flex">
                                <el-button type="primary" @click.prevent="onSearch" :disabled="!dateRange">Search</el-button>
                                <div v-if="allowDownload && tasks.data.length" class="ml-3">
                                    <el-button type="primary" :icon="Download" @click.prevent="onDownload(FileTypes.CSV)">CSV</el-button>
                                    <el-button type="primary" :icon="Download" @click.prevent="onDownload(FileTypes.XLSX)">XLSX</el-button>
                                    <el-button type="primary" :icon="Download" @click.prevent="onDownload(FileTypes.PDF)">PDF</el-button>
                                </div>
                            </div>
                        </div>
                        <div>
                            <el-button type="primary" @click="isCreateTaskDialogVisible = true">Create</el-button>
                            <el-dialog
                                v-model="isCreateTaskDialogVisible"
                                title="Create Task"
                                width="50vw"
                            >
                                <div class="flex flex-col gap-4">
                                    <form @submit.prevent="onCreate">
                                        <div class="mb-4">
                                            <el-input v-model="form.title" placeholder="Title"/>
                                            <div class="text-xs text-red-600 mt-1" v-if="form.errors.title">
                                                {{ form.errors.title }}
                                            </div>
                                        </div>
                                        <div class="mb-4">
                                            <el-input v-model="form.comment" placeholder="Comment"/>
                                            <div class="text-xs text-red-600 mt-1" v-if="form.errors.title">
                                                {{ form.errors.comment }}
                                            </div>
                                        </div>
                                        <div class="mb-4">
                                            <el-date-picker
                                                v-model="form.date"
                                                type="date"
                                                placeholder="Date"
                                                value-format="YYYY-MM-DD"
                                                :style="{width: '100%'}"
                                            />
                                            <div class="text-xs text-red-600 mt-1" v-if="form.errors.date">
                                                {{ form.errors.date }}
                                            </div>
                                        </div>
                                        <div class="mb-4">
                                            <el-input-number v-model="form.time_spent" placeholder="Time Spent"
                                                             :style="{width: '100%'}" :min="1"/>
                                            <span class="text-xs text-gray-400">In minutes</span>
                                            <div class="text-xs text-red-600 mt-1" v-if="form.errors.time_spent">
                                                {{ form.errors.time_spent }}
                                            </div>
                                        </div>
                                        <el-button type="primary" native-type="submit" :disabled="form.processing">
                                            Create
                                        </el-button>
                                    </form>
                                </div>
                            </el-dialog>
                        </div>
                    </div>
                    <el-table :data="tasks.data" :stripe="true">
                        <el-table-column type="expand">
                            <template #default="props">
                                <div class="px-4 py-2">
                                    <span class="block"><strong>Comment</strong></span>
                                    {{ props.row.comment }}
                                </div>
                            </template>
                        </el-table-column>
                        <el-table-column prop="title" label="Title"/>
                        <el-table-column prop="date" label="Date"/>
                        <el-table-column prop="time_spent" label="Time Spent (in minutes)"/>
                    </el-table>
                    <pagination class="mt-6" :links="tasks.links"/>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
