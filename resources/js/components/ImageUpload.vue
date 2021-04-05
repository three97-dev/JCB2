<template>
  <div>
    <a
        @click="toggleShow"
        :class="[position ? 'absolute' : 'relative', dark ? 'white--text' : '']"
        class="btn p-2"
        >Click to add
    </a>
    <my-upload
        @crop-success="cropSuccess"
        @crop-upload-success="cropUploadSuccess"
        @crop-upload-fail="cropUploadFail"
        @src-file-set="srcFileSet"
        v-model="show"
        :width="Number(width)"
        :height="Number(height)"
        :img-format="filetype"
        :url="'/api/fake-upload'"
        no-circle
        field="base64_image"
        lang-type="en"
    ></my-upload>
  </div>
</template>

<script>
export default {
  name: 'ImageUpload',
  props: {
    value: {
      type: Number,
      default: null
    },
    uid: {
      type: Number,
      default: null
    },
    imagetype: {
       type: String,
        default: null
    },
    width: {
      type: [String],
      default: '65'
    },
    height: {
      type: String,
      default: '62'
    },
    position: {
      type: Boolean,
      default: false
    },
    dark: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      server: process.env.NUXT_ENV_SERVER,
      show: false,
      imgDataUrl: null,
      provider_id: null,
      imgName: this.imagetype,
      filetype: 'png'
    }
  },
  methods: {
    toggleShow() {
      this.show = !this.show
    },
    srcFileSet(fileName, fileType, fileSize) {
      fileType = fileType.split('/')
      this.filetype = fileType[1]
    },

    /**
     * crop success
     *
     * [param] imgDataUrl
     * [param] field
     */
    async cropSuccess(imgDataUrl, field) {
      try {
        const res = await this.$axios.post('/upload-image', {
          name: this.imgName,
          filetype: this.filetype,
          base64_image: imgDataUrl
        })
        if (res) {
          this.imgDataUrl = res.data.fileName
          console.log('this.uid', this.uid)
          if (this.uid !== null) {
            this.$emit('image', { filename: res.data.fileName, uid: this.uid })
          } else {
            this.$emit('image', res.data.fileName)
          }
        } else {
          this.$awn.warning('Unable to upload image, incorrect format!')
        }
      } catch (e) {
        this.$awn.warning('Server Error!')
      }
    },
    /**
     * upload success
     *
     * [param] jsonData  server api return data, already json encode
     * [param] field
     */
    cropUploadSuccess(jsonData, field) {},
    /**
     * upload fail
     *
     * [param] status    server api return error status, like 500
     * [param] field
     */
    cropUploadFail(status, field) {
      this.$awn.alert('Uploading failed: ' + status)
    }
  }
}
</script>

<style></style>
