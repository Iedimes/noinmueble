import AppForm from '../app-components/Form/AppForm';

Vue.component('beneficiario-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                PerCod:  '' ,
                PylCod:  '' ,
                CliNop:  '' ,
                CliSec:  '' ,
                CliEsv:  '' ,
                ManCod:  '' ,
                VivLote:  '' ,
                VivBlo:  '' ,
                CliCMor:  '' ,
                
            }
        }
    }

});