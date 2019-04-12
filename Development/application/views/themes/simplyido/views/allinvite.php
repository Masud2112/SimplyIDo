<?php
   /**
   * Added By : Vaidehi
   * Dt : 01/09/2018
   * View Invite Screen
   */
   ?>
  <div id="wrapper" class="invitedetaildashboard">
    <div class="content invitedetails">      
      <div class="row">
        <div class="col-md-12 widget-holder">
          <div class="widget-bg lead-details">
            <div class="widget-heading">
              <h3>Invite List</h3>
            </div>
            <div class="widget-body clearfix">
              <div class="weather-card-default">
                <table class="table table-bordered table-condensed">
                  <thead>
                    <tr>
                      <th>No.</th>
                      <th><?php echo _l('project_name'); ?></th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      $i = 0;
                      foreach ($invitelist as $invite) 
                      {
                        $i++;
                    ?>
                      <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $invite['project_name']; ?></td>
                        <td>
                          <a href="<?php echo base_url('clients/viewinvite/'.$invite["inviteid"]);?>" class="btn btn-default">View Invite</a>
                        </td>
                      </tr>
                    <?php
                      }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>