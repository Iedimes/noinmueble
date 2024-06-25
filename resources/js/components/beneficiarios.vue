<template>
    <div>
      <div class="row">
        <div class="col">
          <div class="card">
            <div class="card-header">
              <i class="fa fa-align-justify"></i> CONSTANCIA DE NO POSEER INMUEBLE
            </div>
            <div class="card-body" v-cloak>
              <div class="card-block">
                <div class="row justify-content-md-between">
                  <div class="col col-lg-7 col-xl-5 form-group">
                    <div class="input-group">
                      <input class="form-control" ref="search" v-model="inputSat" @keyup.enter="getPostulante()" placeholder="Ingrese Documento" />
                      <span class="input-group-append">
                        <button type="button" class="btn btn-primary" @click="getPostulante()"><i class="fa fa-search"></i>&nbsp; Buscar</button>
                      </span>
                    </div>
                  </div>
                </div>
                <div v-if="error">
                  <div class="row">
                    <div class="form-group col-sm-12">
                      <p class="alert alert-danger"><strong>{{ error }}</strong></p>
                    </div>
                  </div>
                </div>

                <div v-if="visible">
                  <hr>
                  <div><h2 style="color: #4273FA; font-weight: bolder;">DATOS PERSONALES</h2><hr></div>
                  <br>
                  <div class="row">
                    <div class="form-group col-sm-4">
                      <p class="card-text"><h5><strong>NOMBRE:</strong> {{ Titular }}</h5></p>
                    </div>
                    <div class="form-group col-sm-3">
                      <p class="card-text"><h5><strong>CEDULA:</strong> {{ Cedula }}</h5></p>
                    </div>
                    <div class="form-group col-sm-5" v-if="Mensaje === ''">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class="btn btn-sm btn-primary pull-center m-b-0 rounded-pill" :href="'/admin/beneficiarios/' + this.Cedula + '/constanciapdf/'"><i class="fa fa-file-pdf-o "></i>&nbsp;<b>GENERAR CONSTANCIA</b></a>
                    </div>
                    <div class="form-group col-sm-5" v-else>
                      <p class="card-text"> <h5>{{ Mensaje }}</h5></p>
                    </div>


                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
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
            <div class="modal-footer ">
              <button type="button" class="btn btn-secondary bg-primary text-white" data-dismiss="modal">Salir</button>
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
        } else {
          let me = this;
          var url = "/admin/beneficiarios/" + this.inputSat;
          this.isLoading = true;

          axios
            .get(url)
            .then(function (response) {
              var respuesta = response.data;
              console.log(respuesta);
              if (!respuesta.error) {
                me.Cedula = respuesta.cedula;
                me.Titular = respuesta.titular;
                me.Mensaje = respuesta.mensaje;
                me.visible = true;
                me.error = "";
              } else {
                console.log("vacio");
                me.visible = false;
                me.error = respuesta.error;
              }
              me.isLoading = false;
            })
            .catch(function (error) {
              console.log(error);
              me.isLoading = false;
            });
        }
      },
    },
  };
  </script>
