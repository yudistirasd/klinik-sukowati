@extends('layouts.app')
@section('title', 'Dashboard')
@section('subtitle', 'Admin')

@section('content')
  <div class="row row-deck row-cards" x-data="dashboard">
    <div class="col-12">
      <div class="row row-cards">
        <div class="col-sm-6 col-lg-3">
          <div class="card card-sm">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col-auto">
                  <span class="bg-azure-lt text-white avatar"><!-- Download SVG icon from http://tabler.io/icons/icon/currency-dollar -->
                    <i class="ti ti-users"></i>
                  </span>
                </div>
                <div class="col">
                  <div class="spinner-border" x-show="loading" x-cloak></div>
                  <div class="fw-bold" x-show="!loading" x-text="scorecard.pasien" x-cloak>0</div>
                  <div class="text-secondary">Pasien</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="card card-sm">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col-auto">
                  <span class="bg-azure-lt text-white avatar"><!-- Download SVG icon from http://tabler.io/icons/icon/currency-dollar -->
                    <i class="ti ti-stethoscope"></i>
                  </span>
                </div>
                <div class="col">
                  <div class="spinner-border" x-show="loading" x-cloak></div>
                  <div class="fw-bold" x-show="!loading" x-text="scorecard.nakes" x-cloak>0</div>
                  <div class="text-secondary">Tenaga Kesehatan</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="card card-sm">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col-auto">
                  <span class="bg-azure-lt text-white avatar"><!-- Download SVG icon from http://tabler.io/icons/icon/currency-dollar -->
                    <i class="ti ti-medicine-syrup"></i>
                  </span>
                </div>
                <div class="col">
                  <div class="spinner-border" x-show="loading" x-cloak></div>
                  <div class="fw-bold" x-show="!loading" x-text="scorecard.obat" x-cloak>0</div>
                  <div class="text-secondary">Obat</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="card card-sm">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col-auto">
                  <span class="bg-azure-lt text-white avatar"><!-- Download SVG icon from http://tabler.io/icons/icon/currency-dollar -->
                    <i class="ti ti-folder-dollar"></i>
                  </span>
                </div>
                <div class="col">
                  <div class="spinner-border" x-show="loading" x-cloak></div>
                  <div class="fw-bold" x-show="!loading" x-text="scorecard.tindakan" x-cloak>0</div>
                  <div class="text-secondary">Tindakan</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

@endsection

@push('js')
  <script>
    console.log("FILE JS DIMUAT");
    document.addEventListener('alpine:init', () => {
      Alpine.data('dashboard', () => ({
        scorecard: {
          pasien: 0,
          nakes: 0,
          obat: 0,
          tindakan: 0
        },
        loading: true,

        init() {
          setTimeout(() => {
            $.ajax({
              url: route('api.dashboard.scorecard.admin'),
              method: 'GET'
            }).done((response) => {
              let scoreCard = response.data;

              this.scorecard.pasien = scoreCard.pasien;
              this.scorecard.nakes = scoreCard.nakes;
              this.scorecard.obat = scoreCard.obat;
              this.scorecard.tindakan = scoreCard.tindakan;

              this.loading = false;
            })
          }, 500);
        }
      }))
    })
  </script>
@endpush
