<template>
  <div>
    <div class="row">
      <div class="col">
        <div class="card">
          <div class="card-header">
            <i class="fa fa-align-justify"></i> CONSTANCIA DE NO SER BENEFICIARIO
          </div>
          <div class="card-body" v-cloak>
            <div class="card-block">
              <!-- 🔍 Input de búsqueda -->
              <div class="row justify-content-md-between">
                <div class="col col-lg-7 col-xl-5 form-group">
                  <div class="input-group">
                    <input
                      class="form-control"
                      ref="search"
                      v-model="inputSat"
                      @keyup.enter="getPostulante"
                      placeholder="Ingrese Documento"
                    />
                    <span class="input-group-append">
                      <button
                        type="button"
                        class="btn btn-primary"
                        :disabled="isLoading"
                        @click="getPostulante"
                      >
                        <i v-if="!isLoading" class="fa fa-search"></i>
                        <i v-else class="fa fa-spinner fa-spin"></i>&nbsp;
                        {{ isLoading ? "Buscando..." : "Buscar" }}
                      </button>
                    </span>
                  </div>
                </div>
              </div>

              <!-- ⚠️ Mensaje de error -->
              <div v-if="error">
                <div class="row">
                  <div class="form-group col-sm-12">
                    <p class="alert alert-danger text-center">
                      <strong>{{ error }}</strong>
                    </p>
                  </div>
                </div>
              </div>

              <!-- 🧾 Datos personales -->
              <div v-if="visible && !error">
                <hr />
                <div>
                  <h2 style="color: #4273FA; font-weight: bolder;">
                    DATOS PERSONALES
                  </h2>
                  <hr />
                </div>
                <br />
                <div class="row">
                  <div class="form-group col-sm-4">
                    <p class="card-text">
                      <h5><strong>NOMBRE:</strong> {{ Titular || "—" }}</h5>
                    </p>
                  </div>
                  <div class="form-group col-sm-3">
                    <p class="card-text">
                      <h5><strong>CEDULA:</strong> {{ Cedula }}</h5>
                    </p>
                  </div>

                  <!-- 🔐 Solo mostrar botón si hay nombre y mensaje vacío -->
                  <div class="form-group col-sm-5" v-if="Mensaje === '' && Titular">
                    <a
                      class="btn btn-sm btn-primary pull-center m-b-0 rounded-pill"
                      :href="'/admin/beneficiarios/' + Cedula + '/constanciapdf/'"
                    >
                      <i class="fa fa-file-pdf-o"></i>&nbsp;<b>GENERAR CONSTANCIA</b>
                    </a>
                  </div>

                  <!-- 📜 Mostrar mensaje o aviso si no se puede imprimir -->
                  <div class="form-group col-sm-5" v-else>
                    <p class="card-text text-danger">
                      <h5>
                        {{ Mensaje || "No se pudieron obtener datos. Intente nuevamente." }}
                      </h5>
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de validación -->
    <div class="modal" id="myModal" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title text-primary"><strong>Error!</strong></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body text-primary">
            <p><strong>El número de documento del beneficiario es requerido.</strong></p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary bg-primary text-white" data-dismiss="modal">
              Salir
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import "vue-js-modal/dist/styles.css";

export default {
  props: ["data"],
  data() {
    return {
      isLoading: false,
      Cedula: "",
      Titular: "",
      Mensaje: "",
      error: "",
      inputSat: "",
      visible: false,
    };
  },
  methods: {
    getPostulante() {
      if (!this.inputSat) {
        $("#myModal").modal("show");
        return;
      }

      this.isLoading = true;
      this.error = "";
      this.visible = false;
      const url = `/admin/beneficiarios/${this.inputSat}`;

      axios
        .get(url)
        .then((response) => {
          const respuesta = response.data;
          console.log(respuesta);

          if (respuesta.error) {
            // Si hay error en la API o no se recuperó nombre
            this.error = respuesta.error;
            this.visible = false;
          } else if (!respuesta.titular) {
            // Si no hay titular aunque no haya error formal
            this.error = "No se pudieron obtener datos. Intente nuevamente.";
            this.visible = false;
          } else {
            // Caso exitoso
            this.Cedula = respuesta.cedula;
            this.Titular = respuesta.titular;
            this.Mensaje = respuesta.mensaje;
            this.visible = true;
            this.error = "";
          }
        })
        .catch((err) => {
          console.error(err);
          this.error = "Error de conexión. Intente nuevamente.";
          this.visible = false;
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
  },
};
</script>
