<?php
include 'inc/header.php';
include 'inc/sidebar.php';
?>
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Community</h1>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12 col-sm-12 col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Community Tab</h4>
                        </div>
                        <div class="card-body">
                            <ul class="nav nav-pills" id="myTab3" role="tablist">
                                <li class="nav-item"><a class="nav-link active" id="Community-tab3" data-toggle="tab" href="#Community" role="tab" aria-controls="Community" aria-selected="true">Community</a></li>
                                <li class="nav-item"><a class="nav-link" id="Contact-tab3" data-toggle="tab" href="#Contact" role="tab" aria-controls="Contact" aria-selected="false">Contact</a></li>
                            </ul>
                            <div class="error-text" style="margin-top: 5px; display: none"></div>
                            <div class="tab-content" id="myTabContent2">
                                <div class="tab-pane fade show active" id="Community" role="tabpanel" aria-labelledby="home-tab3">
                                <div class="table-responsive">
                                <table class="table table-striped" id="communityTable" style="width: 100%">
                                   <thead>
                                     <tr>
                                       <th class="text-center">
                                           #
                                       </th>
                                       <th>Email</th>
                                       <th>Created_at</th>
                                       <th>Status</th>
                                     </tr>
                                   </thead>
                                   <tbody></tbody>
                                 </table>
                                 </div>
                                </div>
                                <div class="tab-pane fade" id="Contact" role="tabpanel" aria-labelledby="profile-tab3">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="contactTable" style="width: 100%">
                                      <thead>
                                        <tr>
                                          <th class="text-center">
                                            #
                                          </th>
                                          <th>Name</th>
                                          <th>Email</th>
                                          <th>Phone</th>
                                          <th>Address</th>
                                          <th>Note</th>
                                          <th>Send_on</th>
                                        </tr>
                                      </thead>
                                      <tbody></tbody>
                                    </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>


<?php
include 'inc/footer.php'; 
?>
<script src="../ajax/js/community.js"></script>