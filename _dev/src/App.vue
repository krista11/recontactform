<template>
  <form @submit.prevent="submitForm" class="col-sp-12" v-if="config">
    <!-- Form Fields -->
    <div class="form-group row">
      <label class="col-md-4 form-control-label">{{ config.types.name }}</label>
      <div class="col-md-8">
        <select v-model="selectedFormType" class="form-control">
          <option v-for="(option, key) in config.types.options" :key="key" :value="key">
            {{ option.name }}
          </option>
        </select>
      </div>
    </div>

    <div v-for="(field, key) in config.fields" :key="key" class="form-group row" :class="{ 'has-error': errors[key] }">
      <label :for="key" class="col-md-4 form-control-label">
        {{ field.name }}
        <span v-if="isFieldRequired(field.required)" style="color: red;">*</span>
      </label>
      <div class="col-md-8">
        <input
            v-if="field.type === 'text' || field.type === 'email'"
            v-model="formData[key]"
            :type="field.type"
            :id="key"
            :required="isFieldRequired(field.required)"
            class="form-control"
            @change="validateField(key)"
        />

        <textarea
            v-if="field.type === 'textarea'"
            v-model="formData[key]"
            :id="key"
            :required="isFieldRequired(field.required)"
            class="form-control"
            rows="4" cols="50"
            maxlength="512"
        />

        <input
            v-if="field.type === 'date'"
            v-model="formData[key]"
            :type="field.type"
            :id="key"
            :required="isFieldRequired(field.required)"
            :min="todayDate"
            class="form-control form-control-date"
        />

        <input
            v-if="field.type === 'file'"
            @change="handleFileUpload($event, key)"
            :type="field.type"
            :id="key"
            :required="isFieldRequired(field.required)"
            class="form-control-file filestyle"
            accept="image/png, image/jpeg"
        />
        <!-- Link -->
        <a v-if="field.type === 'file' && config.link.url" :href="config.link.url">{{ config.link.name }}</a>
      </div>
    </div>

    <div class="form-footer text-sp-right row">
      <div v-if="successMessage" class="alert alert-success">
        {{ successMessage }}
      </div>
      <div v-if="errorMessage" class="alert alert-danger">
        {{ errorMessage }}
      </div>
      <div v-if="isLoading" class="loader"></div>
      <button type="submit" class="btn btn-outline">Send Message</button>
    </div>
  </form>
</template>

<script>
export default {
  name: 'App',
  data() {
    return {
      config: null,
      selectedFormType: '',
      todayDate: this.getTodayDate(),
      formData: {},
      errors: {},
      successMessage: '',
      errorMessage: '',
      isLoading: false,
      recaptchaToken: '',
    };
  },
  mounted() {
    if (this.config.reCaptchaEnabled) {
      const script = document.createElement('script');
      script.src = 'https://www.google.com/recaptcha/api.js?render=' + this.config.siteKey;
      script.async = true;
      script.onload = () => {
        console.log('reCAPTCHA script loaded.');
      };
      script.onerror = () => {
        console.error('Failed to load reCAPTCHA script.');
      };
      document.head.appendChild(script);
    }
  },
  created() {
    const configData = document.getElementById('app-recontact-form').getAttribute('data-form-config');
    this.config = JSON.parse(configData);
    this.selectedFormType = Object.keys(this.config.types.options)[0];
  },
  methods: {
    isFieldRequired(requiredTypes) {
      return requiredTypes.includes(this.selectedFormType);
    },
    getTodayDate() {
      const today = new Date();
      return `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
    },
    handleFileUpload(event, key) {
      this.formData[key] = event.target.files[0];
    },
    validateField(key) {
      if (key === 'phone') {
        const phoneNumberPattern = /^[+0-9. ()/-]*$/;

        if (!phoneNumberPattern.test(this.formData[key])) {
          this.errors[key] = 'Invalid phone number format.';
        } else {
          this.errors[key] = '';
        }
      }

      if (!Object.values(this.errors).some(error => error)) {
        this.errorMessage = '';
      }
    },
    async loadRecaptcha() {
      return new Promise((resolve, reject) => {
        if (typeof window.grecaptcha !== 'undefined') {
          window.grecaptcha.ready(() => {
            window.grecaptcha.execute(this.config.siteKey, { action: 'submit' })
                .then(token => {
                  this.recaptchaToken = token;
                  resolve(token);
                })
                .catch(error => {
                  console.error('reCAPTCHA execution failed:', error);
                  reject(error);
                });
          });
        } else {
          console.error('reCAPTCHA is not defined.');
          reject('reCAPTCHA is not defined');
        }
      });
    },
    async submitForm() {
      this.isLoading = true;

      if (this.config.reCaptchaEnabled) {
        await this.loadRecaptcha();
      }

      this.validateField('phone');

      if (Object.values(this.errors).some(error => error)) {
        this.errorMessage = this.config.errorMessage;
        this.isLoading = false;
      } else {
        const formData = new FormData();
        formData.append('formType', this.selectedFormType);
        formData.append('action', 'submitContact');
        formData.append('g-recaptcha-response', this.recaptchaToken);

        for (const key in this.formData) {
          if (Object.prototype.hasOwnProperty.call(this.formData, key)) {
            formData.append(key, this.formData[key]);
          }
        }

        try {
          const response = await fetch(this.config.action, {
            method: 'POST',
            body: formData,
          });

          const data = await response.json();

          if (data.success) {
            this.successMessage = data.message;
            Object.keys(this.formData).forEach((field) => {
              this.formData[field] = '';
            });
            this.isLoading = false;
          } else {
            this.errorMessage = data.message;
            this.isLoading = false;
          }
        } catch (error) {
          this.errorMessage = 'Something went wrong';
          this.isLoading = false;
          console.error('There was an error submitting the form!', error);
        }
      }
    },
  },
};
</script>

<style scoped>
.form-control-date {
  width: auto;
}
.loader {
  width: 48px;
  height: 48px;
  border: 5px solid #eceeef;
  border-bottom-color: #FFF;
  border-radius: 50%;
  animation: rotation 1s linear infinite;
  margin: 0 0 10px auto;
}

@keyframes rotation {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}
</style>