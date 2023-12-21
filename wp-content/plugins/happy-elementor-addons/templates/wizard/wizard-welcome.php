<div class="inner-content">
    <svg width="110" height="118" viewBox="0 0 110 118" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M101.142 27.7645C102.172 27.7645 102.996 27.5542 104.026 27.5542C105.88 27.3438 107.116 25.6611 106.91 23.9784C106.704 22.0854 105.056 20.8233 103.408 21.0337C90.6367 22.5061 78.4831 14.7235 74.5693 2.31354C73.9513 0.63083 72.0974 -0.420863 70.4494 0.210153C68.8015 0.841169 67.7715 2.73422 68.3895 4.41693C72.9213 18.72 86.5168 28.3955 101.142 27.7645Z" fill="#E2498A"/>
        <path d="M105.88 40.5953C104.85 38.2816 102.584 36.8092 100.112 36.8092C96.8165 37.0196 93.3146 36.8092 89.8127 35.9679C75.3933 33.0231 64.4757 22.7165 59.5318 9.67555C58.7079 7.36183 56.236 5.67912 53.764 5.88945C26.985 6.52047 3.70787 26.9233 0.411986 55.5294C-2.47191 81.8217 13.3895 107.062 37.9026 115.266C69.6255 125.782 102.378 105.8 108.97 73.1978C111.236 61.8395 109.794 50.4813 105.88 40.5953ZM63.8577 44.8021C64.2697 43.1194 65.9176 41.8574 67.7715 42.278L81.367 45.2228C83.015 45.6435 84.2509 47.3262 83.839 49.2192C83.427 50.9019 81.779 52.164 79.9251 51.7433L66.3296 48.7985C64.6816 48.1675 63.4457 46.4848 63.8577 44.8021ZM33.7828 40.385C34.6067 36.1782 38.7266 33.4438 42.8464 34.2852C46.9663 35.1265 49.6442 39.3333 48.8202 43.5401C47.9963 47.7468 43.8764 50.4813 39.7566 49.6399C35.6367 48.7985 32.9588 44.5918 33.7828 40.385ZM86.5169 79.2977C79.7191 95.7041 61.5918 104.959 43.8764 99.0695C30.6929 94.6524 21.4232 82.2424 20.1873 68.5704C19.9813 64.9946 22.8652 62.0499 26.367 62.6809L82.397 71.9358C85.6929 72.5668 87.7528 76.1426 86.5169 79.2977Z" fill="#E2498A"/>
        <path d="M58.9139 83.9251C52.1161 82.4528 45.5243 85.1872 41.8165 90.2353C40.9925 91.287 41.4045 92.9697 42.6404 93.3904C44.7004 94.4421 47.1723 95.2834 49.6442 95.9144C56.236 97.3868 62.8277 96.1248 68.1835 93.18C69.4195 92.549 69.6255 90.8663 68.8015 89.8146C66.3296 86.8699 62.8277 84.7665 58.9139 83.9251Z" fill="#E2498A"/>
    </svg>
    <h2 class="title-big color-purple">Welcome to Happy Addons!</h2>
    <?php 
        if (!class_exists('WP_Site_Health')) {
            require_once ABSPATH . 'wp-admin/includes/class-wp-site-health.php';
        }

        $info = [];

        if (WP_Site_Health::get_instance()->php_memory_limit !== ini_get('memory_limit')) {
            $info['memory_limit'] = WP_Site_Health::get_instance()->php_memory_limit;
            $info['admin_memory_limit'] = ini_get('memory_limit');
        } else {
            $info['memory_limit'] = ini_get('memory_limit');
        }

        $hasLowMem = (str_replace("M",'',$info['memory_limit']) < 256)?true:false;
    ?>
    <div class="php-info">Your current PHP Memory Limit: <strong><?=$info['memory_limit']?></strong>
        <?php if($hasLowMem): ?>
        <p>(Increase your memory limit, or you can balance it by disabling the unused widgets and features)</p>
        <?php endif; ?>
    </div>

    <div class="welcome-buttongroup">
        <div 
            class="switch"
            :class="{ active: userType==='normal' }"
            @click="setUserType('normal')">
            <span class="radio"></span>
            <div class="switch-data">
                <span class="title">I’m a regular User</span>
                <span class="description">Configure it for me</span>
            </div>
        </div>
        <div 
            class ="switch"
            :class="{ active: userType==='pro' }"
            @click="setUserType('pro')">
            <span class="radio"></span>
            <div class="switch-data">
                <span class="title">I’m a power User</span>
                <span class="description">Let me configure it myself</span>
            </div>
        </div>
    </div>
    
    <ha-nav
    prev=""
    next="widgets"
    done=""
    @set-tab="setTab"
    ></ha-nav>
    
    <span class="skip-setup" @click="endWizard()">Skip Setup & Go to Dashboard</span>
</div>