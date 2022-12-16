<template>
    <v-form>
        <v-container>
            <v-row class="d-flex flex-column align-center">
                <v-col
                    cols="12"
                    sm="6"
                    md="4"
                >
                    <v-text-field
                        v-model="fullName"
                        :rules="[rules.required]"
                        label="Full name"
                        outlined
                        dense
                    ></v-text-field>
                </v-col>
                <v-col
                    cols="12"
                    sm="6"
                    md="4"
                >
                    <v-select
                        v-model="selectedCountry"
                        :items="countries"
                        @change="countrySelectAction"
                        item-text="name"
                        label="Outlined style"
                        dense
                        outlined
                    >
                        <template v-slot:item="slotProps">
                            <i :class="['mr-4', 'em', slotProps.item.flag]"></i>
                            {{slotProps.item.name}}
                        </template>
                    </v-select>
                </v-col>
                <v-col
                    cols="12"
                    sm="6"
                    md="4"
                >
                    <v-text-field
                        :value="phoneNumber"
                        :rules="[rules.required, rules.number]"
                        @input="phoneNumberAction"
                        label="Phone number"
                        outlined
                        dense
                    ></v-text-field>
                </v-col>
                <v-col
                    cols="12"
                    sm="6"
                    md="4"
                >
                    <v-text-field
                        v-model="email"
                        :rules="[rules.required, rules.email]"
                        label="Email"
                        outlined
                        dense
                    ></v-text-field>
                </v-col>
                <v-col
                    cols="12"
                    sm="6"
                    md="4"
                >
                    <v-text-field
                        v-model="password"
                        :rules="[rules.required]"
                        type="password"
                        label="Password"
                        outlined
                        dense
                    ></v-text-field>
                </v-col>
                <v-col
                    class="d-flex justify-space-between"
                    cols="12"
                    sm="6"
                    md="4"
                >
                    <v-btn
                        @click="registerAction"
                        color="primary"
                        class="my-2"
                    >
                        <span>Register</span>
                    </v-btn>
                </v-col>
            </v-row>
        </v-container>
    </v-form>
</template>

<script>
import axios from 'axios';
import EmojiConvertor from 'emoji-js';

export default {
    name: 'RegisterComponent.vue',
    data() {
        return {
            selectedCountry: null,
            fullName: '',
            phoneNumber: '',
            email: '',
            password: '',
            countries: [],
            rules: {
                required: value => !!value || 'Required.',
                email: value => {
                    const pattern = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
                    return pattern.test(value) || 'Invalid e-mail.'
                },
                number: value => {
                    const pattern = /^\+[0-9]{2,4} ?[0-9- ]{0,14}$/
                    return pattern.test(value) || 'Invalid phone number'
                }
            },
        }
    },
    async mounted() {
        const { data } = await axios.get('/storage/countries.json');

        const emoji = new EmojiConvertor();
        emoji.text_mode = true;

        this.countries = data.map(el => {
            return {
                name: el['name'],
                idd: el['idd'],
                flag: `em-${emoji.replace_unified(el['flag'])}`
            }
        });

        this.selectedCountry = this.countries[0];
        this.phoneNumber = `${this.countries[0]['idd']} `;
    },
    methods: {
        async registerAction() {
            const { data: { status, message } } = await axios.post('/api/register', {
                'full_name': this.fullName,
                'country': this.selectedCountry['name'],
                'phone_number': this.phoneNumber,
                'email': this.email,
                'password': this.password,
            });

            if (status === 'success') {
                window.location.href = '/';
            } else {
                console.error(message);
            }
        },
        countrySelectAction(country) {
            const idd = this.countries.filter(el => el['name'] === country)[0]['idd'];
            this.phoneNumber = `${idd} `;
        },
        phoneNumberAction(e) {
            this.phoneNumber = e;
        }
    }
}
</script>

<style scoped>

</style>
