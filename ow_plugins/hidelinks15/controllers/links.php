<?php

class HIDELINKS_CTRL_Links extends OW_ActionController
{

    public function awayto($params)
    {
        if (empty($params["href"])) 
        {
            throw new Redirect404Exception();
        }
        $href = base64_decode($params["href"]);
        if (empty($href)) 
        {
            throw new Redirect404Exception();
        }
        $this->redirect($href);
    }

}
