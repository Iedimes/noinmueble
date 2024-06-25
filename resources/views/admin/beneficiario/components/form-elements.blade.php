<div class="form-group row align-items-center" :class="{'has-danger': errors.has('PerCod'), 'has-success': fields.PerCod && fields.PerCod.valid }">
    <label for="PerCod" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.beneficiario.columns.PerCod') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.PerCod" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('PerCod'), 'form-control-success': fields.PerCod && fields.PerCod.valid}" id="PerCod" name="PerCod" placeholder="{{ trans('admin.beneficiario.columns.PerCod') }}">
        <div v-if="errors.has('PerCod')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('PerCod') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('PylCod'), 'has-success': fields.PylCod && fields.PylCod.valid }">
    <label for="PylCod" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.beneficiario.columns.PylCod') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.PylCod" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('PylCod'), 'form-control-success': fields.PylCod && fields.PylCod.valid}" id="PylCod" name="PylCod" placeholder="{{ trans('admin.beneficiario.columns.PylCod') }}">
        <div v-if="errors.has('PylCod')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('PylCod') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('CliNop'), 'has-success': fields.CliNop && fields.CliNop.valid }">
    <label for="CliNop" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.beneficiario.columns.CliNop') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.CliNop" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('CliNop'), 'form-control-success': fields.CliNop && fields.CliNop.valid}" id="CliNop" name="CliNop" placeholder="{{ trans('admin.beneficiario.columns.CliNop') }}">
        <div v-if="errors.has('CliNop')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('CliNop') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('CliSec'), 'has-success': fields.CliSec && fields.CliSec.valid }">
    <label for="CliSec" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.beneficiario.columns.CliSec') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.CliSec" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('CliSec'), 'form-control-success': fields.CliSec && fields.CliSec.valid}" id="CliSec" name="CliSec" placeholder="{{ trans('admin.beneficiario.columns.CliSec') }}">
        <div v-if="errors.has('CliSec')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('CliSec') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('CliEsv'), 'has-success': fields.CliEsv && fields.CliEsv.valid }">
    <label for="CliEsv" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.beneficiario.columns.CliEsv') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.CliEsv" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('CliEsv'), 'form-control-success': fields.CliEsv && fields.CliEsv.valid}" id="CliEsv" name="CliEsv" placeholder="{{ trans('admin.beneficiario.columns.CliEsv') }}">
        <div v-if="errors.has('CliEsv')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('CliEsv') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('ManCod'), 'has-success': fields.ManCod && fields.ManCod.valid }">
    <label for="ManCod" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.beneficiario.columns.ManCod') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.ManCod" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('ManCod'), 'form-control-success': fields.ManCod && fields.ManCod.valid}" id="ManCod" name="ManCod" placeholder="{{ trans('admin.beneficiario.columns.ManCod') }}">
        <div v-if="errors.has('ManCod')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('ManCod') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('VivLote'), 'has-success': fields.VivLote && fields.VivLote.valid }">
    <label for="VivLote" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.beneficiario.columns.VivLote') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.VivLote" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('VivLote'), 'form-control-success': fields.VivLote && fields.VivLote.valid}" id="VivLote" name="VivLote" placeholder="{{ trans('admin.beneficiario.columns.VivLote') }}">
        <div v-if="errors.has('VivLote')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('VivLote') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('VivBlo'), 'has-success': fields.VivBlo && fields.VivBlo.valid }">
    <label for="VivBlo" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.beneficiario.columns.VivBlo') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.VivBlo" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('VivBlo'), 'form-control-success': fields.VivBlo && fields.VivBlo.valid}" id="VivBlo" name="VivBlo" placeholder="{{ trans('admin.beneficiario.columns.VivBlo') }}">
        <div v-if="errors.has('VivBlo')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('VivBlo') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('CliCMor'), 'has-success': fields.CliCMor && fields.CliCMor.valid }">
    <label for="CliCMor" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.beneficiario.columns.CliCMor') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.CliCMor" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('CliCMor'), 'form-control-success': fields.CliCMor && fields.CliCMor.valid}" id="CliCMor" name="CliCMor" placeholder="{{ trans('admin.beneficiario.columns.CliCMor') }}">
        <div v-if="errors.has('CliCMor')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('CliCMor') }}</div>
    </div>
</div>


