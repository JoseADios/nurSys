<script setup>
import { onMounted, ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import axios from 'axios';
import DateInput from '@/Components/DateInput.vue';
import TextAreaInput from '@/Components/TextAreaInput.vue';
import PhoneInput from '@/Components/PhoneInput.vue';
import CedulaInput from '@/Components/CedulaInput.vue';
import DynamicAvatar from '@/Components/DynamicAvatar.vue';

const props = defineProps({
    user: Object,
    errors: {
        type: Object,
        default: null
    }
});

const form = useForm({
    _method: 'PUT',
    name: props.user.name,
    last_name: props.user.last_name,
    email: props.user.email,
    photo: null,
    identification_card: props.user.identification_card,
    exequatur: props.user.exequatur,
    specialty: props.user.specialty,
    area: props.user.area,
    phone: props.user.phone,
    birthdate: props.user.birthdate,
    position: props.user.position,
    address: props.user.address,
    comment: props.user.comment
});

const verificationLinkSent = ref(null);
const photoPreview = ref(null);
const photoInput = ref(null);
const clinicAreas = ref([]);
const activeTab = ref('personal'); // 'personal', 'professional', 'contact'
const exequaturRequired = ref(false);

onMounted(async () => {
    setExequaturVisibily();
    try {
        const response = await axios.get(route('clinicAreas.index'));
        clinicAreas.value = response.data.map(area => ({ id: area.id, name: area.name }));
    } catch (error) {
        console.error('Error fetching clinic areas:', error);
    }
});

const updateProfileInformation = () => {
    if (photoInput.value) {
        form.photo = photoInput.value.files[0];
    }

    form.post(route('user-profile-information.update'), {
        errorBag: 'updateProfileInformation',
        preserveScroll: true,
        onSuccess: () => clearPhotoFileInput(),
    });
};

const setExequaturVisibily = () => {
    exequaturRequired.value = ['nurse', 'doctor'].includes(props.user.roles[0]);
}

const sendEmailVerification = () => {
    verificationLinkSent.value = true;
};

const selectNewPhoto = () => {
    photoInput.value.click();
};

const updatePhotoPreview = () => {
    const photo = photoInput.value.files[0];

    if (!photo) return;

    const reader = new FileReader();

    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };

    reader.readAsDataURL(photo);
};

const deletePhoto = () => {
    router.delete(route('current-user-photo.destroy'), {
        preserveScroll: true,
        onSuccess: () => {
            photoPreview.value = null;
            clearPhotoFileInput();
        },
    });
};

const clearPhotoFileInput = () => {
    if (photoInput.value?.value) {
        photoInput.value.value = null;
    }
};

const hasErrorsInTab = (tabName) => {
    const errorFields = {
        personal: ['name', 'last_name', 'email', 'photo', 'identification_card', 'birthdate'],
        professional: ['exequatur', 'specialty', 'area', 'position'],
        contact: ['phone', 'address', 'comment']
    };

    // Verificar si hay errores o campos requeridos vacíos
    return errorFields[tabName].some(field => form.errors[field] || (form[field] === null || form[field] === ''));
};

const setActiveTab = (tab) => {
    activeTab.value = tab;
};
</script>

<template>
    <FormSection @submitted="updateProfileInformation">
        <template #title>
            Información del Perfil {{ errors }}
        </template>

        <template #description>
            Actualice la información del perfil y la dirección de correo electrónico de su cuenta.
        </template>

        <template #form>
            <!-- Tabs Navigation - Full width -->
            <div class="col-span-6 border-b border-gray-200 dark:border-gray-700 mb-4">
                <nav class="flex space-x-8" aria-label="Profile Sections">
                    <button @click="setActiveTab('personal')" :class="[
                        'py-4 px-1 border-b-2 font-medium text-sm relative',
                        activeTab === 'personal'
                            ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400'
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'
                    ]" type="button">
                        Información Personal
                        <span v-if="hasErrorsInTab('personal') && activeTab !== 'personal'"
                            class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>

                    <button @click="setActiveTab('professional')" :class="[
                        'py-4 px-1 border-b-2 font-medium text-sm relative',
                        activeTab === 'professional'
                            ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400'
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'
                    ]" type="button">
                        Información Profesional
                        <span v-if="hasErrorsInTab('professional') && activeTab !== 'professional'"
                            class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>

                    <button @click="setActiveTab('contact')" :class="[
                        'py-4 px-1 border-b-2 font-medium text-sm relative',
                        activeTab === 'contact'
                            ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400'
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'
                    ]" type="button">
                        Contacto y Comentarios
                        <span v-if="hasErrorsInTab('contact') && activeTab !== 'contact'"
                            class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>
                </nav>
            </div>

            <!-- Tab Content Container - Full width -->
            <div class="col-span-6">
                <!-- Tab: Personal Information -->
                <div v-show="activeTab === 'personal'">
                    <!-- Profile Photo -->
                    <div v-if="$page.props.jetstream.managesProfilePhotos" class="mb-6">
                        <!-- Profile Photo File Input -->
                        <input id="photo" ref="photoInput" type="file" class="hidden" @change="updatePhotoPreview">

                        <InputLabel for="photo" value="Foto" />

                        <!-- Current Profile Photo -->
                        <div v-show="!photoPreview" class="mt-2">
                            <img v-if="user.profile_photo_url" :src="user.profile_photo_url" :alt="user.name"
                                class="rounded-full size-20 object-cover">

                            <DynamicAvatar v-else :name="user.name" size-class="size-20" />
                        </div>

                        <!-- New Profile Photo Preview -->
                        <div v-show="photoPreview" class="mt-2">
                            <span class="block rounded-full size-20 bg-cover bg-no-repeat bg-center"
                                :style="'background-image: url(\'' + photoPreview + '\');'" />
                        </div>

                        <SecondaryButton class="mt-2 me-2" type="button" @click.prevent="selectNewPhoto">
                            Seleccionar una foto nueva
                        </SecondaryButton>

                        <SecondaryButton v-if="user.profile_photo_path" type="button" class="mt-2"
                            @click.prevent="deletePhoto">
                            Eliminar Foto
                        </SecondaryButton>

                        <InputError :message="form.errors.photo" class="mt-2" />
                    </div>

                    <!-- Two column layout for personal info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left column -->
                        <div>
                            <!-- Name -->
                            <div class="mb-6">
                                <InputLabel for="name" value="Nombre" :required="true" />
                                <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" required
                                    autocomplete="name" />
                                <InputError :message="form.errors.name" class="mt-2" />
                            </div>

                            <!-- Email -->
                            <div class="mb-6">
                                <InputLabel for="email" value="Correo" :required="true" />
                                <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full"
                                    required autocomplete="username" />
                                <InputError :message="form.errors.email" class="mt-2" />

                                <div
                                    v-if="$page.props.jetstream.hasEmailVerification && user.email_verified_at === null">
                                    <p class="text-sm mt-2 dark:text-white">
                                        Su dirección de correo electrónico no está verificada.

                                        <Link :href="route('verification.send')" method="post" as="button"
                                            class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                                            @click.prevent="sendEmailVerification">
                                        Haga clic aquí para reenviar el correo electrónico de verificación.
                                        </Link>
                                    </p>

                                    <div v-show="verificationLinkSent"
                                        class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                                        Se ha enviado un nuevo enlace de verificación a su dirección de correo
                                        electrónico.
                                    </div>
                                </div>
                            </div>

                            <!-- Fecha de nacimiento -->
                            <div>
                                <InputLabel for="birthdate" value="Fecha de nacimiento" :required="true" />
                                <DateInput id="birthdate" v-model="form.birthdate" type="text" class="mt-1 block w-full"
                                    required autocomplete="birthdate" />
                                <InputError :message="form.errors.birthdate" class="mt-2" />
                            </div>
                        </div>

                        <!-- Right column -->
                        <div>
                            <!-- LastName -->
                            <div class="mb-6">
                                <InputLabel for="last_name" value="Apellido" :required="true" />
                                <TextInput id="last_name" v-model="form.last_name" type="text" class="mt-1 block w-full"
                                    required autocomplete="last_name" />
                                <InputError :message="form.errors.last_name" class="mt-2" />
                            </div>

                            <!-- Cedula -->
                            <div>
                                <InputLabel for="identification_card" value="Cédula" :required="true" />
                                <CedulaInput id="identification_card" v-model="form.identification_card" type="text"
                                    class="mt-1 block w-full" required autocomplete="identification_card" />
                                <InputError :message="form.errors.identification_card" class="mt-2" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab: Professional Information -->
                <div v-show="activeTab === 'professional'">
                    <!-- Two column layout for professional info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left column -->
                        <div>
                            <!-- Exequatur -->
                            <div class="mb-6">
                                <InputLabel for="exequatur" value="Exequatur" :required="exequaturRequired" />
                                <TextInput id="exequatur" v-model="form.exequatur" :required="exequaturRequired"
                                    type="text" class="mt-1 block w-full" autocomplete="exequatur" />
                                <InputError :message="form.errors.exequatur" class="mt-2" />
                            </div>

                            <!-- Área -->
                            <div class="mb-6">
                                <InputLabel for="area" value="Área" />
                                <SelectInput v-model:model-value="form.area" :options="clinicAreas" />
                                <InputError :message="form.errors.area" class="mt-2" />
                            </div>
                        </div>

                        <!-- Right column -->
                        <div>
                            <!-- Especialidad -->
                            <div class="mb-6">
                                <InputLabel for="specialty" value="Especialidad" :required="true" />
                                <TextInput id="specialty" v-model="form.specialty" type="text" class="mt-1 block w-full"
                                    required autocomplete="specialty" />
                                <InputError :message="form.errors.specialty" class="mt-2" />
                            </div>

                            <!-- Posición -->
                            <div>
                                <InputLabel for="position" value="Posición" />
                                <TextInput id="position" v-model="form.position" type="text" class="mt-1 block w-full"
                                    required autocomplete="position" />
                                <InputError :message="form.errors.position" class="mt-2" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab: Contact Information -->
                <div v-show="activeTab === 'contact'">
                    <!-- Teléfono -->
                    <div class="mb-6">
                        <InputLabel for="phone" value="Teléfono" :required="true" />
                        <PhoneInput id="phone" v-model="form.phone" type="text" class="mt-1 block w-full" required
                            autocomplete="phone" />
                        <InputError :message="form.errors.phone" class="mt-2" />
                    </div>

                    <!-- Dirección -->
                    <div class="mb-6">
                        <InputLabel for="address" value="Dirección" :required="true" />
                        <TextAreaInput id="address" v-model="form.address" type="text" class="mt-1 block w-full"
                            required autocomplete="address" />
                        <InputError :message="form.errors.address" class="mt-2" />
                    </div>

                    <!-- Comentarios -->
                    <div>
                        <InputLabel for="comment" value="Comentarios" />
                        <TextAreaInput id="comment" v-model="form.comment" type="text" class="mt-1 block w-full"
                            required autocomplete="comment" />
                        <InputError :message="form.errors.comment" class="mt-2" />
                    </div>
                </div>
            </div>
        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="me-3">
                Guardado.
            </ActionMessage>

            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Guardar
            </PrimaryButton>
        </template>
    </FormSection>
</template>
