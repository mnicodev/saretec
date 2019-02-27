<?php

/* modules/contrib/blazy/templates/blazy.html.twig */
class __TwigTemplate_aef4a77e7bcd8e13fadb4fef19273be840946cc8a0e6c47e5e13f3a7085354fd extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = [
            'blazy_player' => [$this, 'block_blazy_player'],
            'blazy_media' => [$this, 'block_blazy_media'],
            'blazy_content' => [$this, 'block_blazy_content'],
            'blazy_caption' => [$this, 'block_blazy_caption'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453 = $this->env->getExtension("Drupal\\webprofiler\\Twig\\Extension\\ProfilerExtension");
        $__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453->enter($__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "modules/contrib/blazy/templates/blazy.html.twig"));

        $tags = ["set" => 27, "if" => 46, "block" => 47, "for" => 89];
        $filters = ["clean_class" => 31];
        $functions = [];

        try {
            $this->env->getExtension('Twig_Extension_Sandbox')->checkSecurity(
                ['set', 'if', 'block', 'for'],
                ['clean_class'],
                []
            );
        } catch (Twig_Sandbox_SecurityError $e) {
            $e->setSourceContext($this->getSourceContext());

            if ($e instanceof Twig_Sandbox_SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof Twig_Sandbox_SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof Twig_Sandbox_SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            }

            throw $e;
        }

        // line 27
        $context["classes"] = [0 => "media", 1 => (($this->getAttribute(        // line 29
($context["settings"] ?? null), "namespace", [])) ? (("media--" . $this->getAttribute(($context["settings"] ?? null), "namespace", []))) : ("")), 2 => (($this->getAttribute(        // line 30
($context["settings"] ?? null), "lazy", [])) ? ("media--loading") : ("")), 3 => (($this->getAttribute(        // line 31
($context["settings"] ?? null), "media_switch", [])) ? (("media--switch media--switch--" . \Drupal\Component\Utility\Html::getClass($this->getAttribute(($context["settings"] ?? null), "media_switch", [])))) : ("")), 4 => (($this->getAttribute(        // line 32
($context["settings"] ?? null), "player", [])) ? ("media--player") : ("")), 5 => (($this->getAttribute(        // line 33
($context["settings"] ?? null), "ratio", [])) ? (("media--ratio media--ratio--" . $this->getAttribute(($context["settings"] ?? null), "ratio", []))) : ("")), 6 => (($this->getAttribute(        // line 34
($context["settings"] ?? null), "responsive_image_style_id", [])) ? ("media--responsive") : ("")), 7 => (($this->getAttribute(        // line 35
($context["settings"] ?? null), "type", [])) ? (("media--" . $this->getAttribute(($context["settings"] ?? null), "type", []))) : (""))];
        // line 39
        $context["iframe_classes"] = [0 => "media__iframe", 1 => (($this->getAttribute(        // line 41
($context["settings"] ?? null), "ratio", [])) ? ("media__element") : (""))];
        // line 44
        echo "
";
        // line 45
        ob_start();
        // line 46
        echo "  ";
        if ($this->getAttribute(($context["settings"] ?? null), "player", [])) {
            // line 47
            echo "    ";
            $this->displayBlock('blazy_player', $context, $blocks);
            // line 54
            echo "  ";
        }
        $context["player"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 56
        echo "
";
        // line 57
        ob_start();
        // line 58
        echo "  ";
        $this->displayBlock('blazy_media', $context, $blocks);
        $context["media"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 66
        echo "
";
        // line 67
        ob_start();
        // line 68
        echo "  ";
        $this->displayBlock('blazy_content', $context, $blocks);
        // line 85
        echo "
  ";
        // line 86
        if ((($context["captions"] ?? null) && $this->getAttribute(($context["captions"] ?? null), "inline", [], "any", true, true))) {
            // line 87
            echo "    ";
            $this->displayBlock('blazy_caption', $context, $blocks);
            // line 96
            echo "  ";
        }
        $context["blazy"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 98
        echo "
";
        // line 99
        if (($context["wrapper_attributes"] ?? null)) {
            // line 100
            echo "  <div";
            echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, ($context["wrapper_attributes"] ?? null), "html", null, true));
            echo ">
    ";
            // line 101
            echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, ($context["blazy"] ?? null), "html", null, true));
            echo "
  </div>
";
        } else {
            // line 104
            echo "  ";
            echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, ($context["blazy"] ?? null), "html", null, true));
            echo "
";
        }
        
        $__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453->leave($__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453_prof);

    }

    // line 47
    public function block_blazy_player($context, array $blocks = [])
    {
        $__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453 = $this->env->getExtension("Drupal\\webprofiler\\Twig\\Extension\\ProfilerExtension");
        $__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453->enter($__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "blazy_player"));

        // line 48
        echo "      <iframe";
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute(($context["iframe_attributes"] ?? null), "addClass", [0 => ($context["iframe_classes"] ?? null)], "method"), "html", null, true));
        echo " allowfullscreen></iframe>
      ";
        // line 49
        if ($this->getAttribute(($context["settings"] ?? null), "media_switch", [])) {
            // line 50
            echo "        <span class=\"media__icon media__icon--close\"></span>
        <span class=\"media__icon media__icon--play\" data-url=\"";
            // line 51
            echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute(($context["settings"] ?? null), "autoplay_url", []), "html", null, true));
            echo "\"></span>
      ";
        }
        // line 53
        echo "    ";
        
        $__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453->leave($__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453_prof);

    }

    // line 58
    public function block_blazy_media($context, array $blocks = [])
    {
        $__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453 = $this->env->getExtension("Drupal\\webprofiler\\Twig\\Extension\\ProfilerExtension");
        $__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453->enter($__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "blazy_media"));

        // line 59
        echo "    <div";
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute(($context["attributes"] ?? null), "addClass", [0 => ($context["classes"] ?? null)], "method"), "html", null, true));
        echo ">
      ";
        // line 60
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, ($context["image"] ?? null), "html", null, true));
        echo "
      ";
        // line 61
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, ($context["player"] ?? null), "html", null, true));
        echo "
      ";
        // line 62
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute(($context["settings"] ?? null), "icon", []), "html", null, true));
        echo "
    </div>
  ";
        
        $__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453->leave($__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453_prof);

    }

    // line 68
    public function block_blazy_content($context, array $blocks = [])
    {
        $__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453 = $this->env->getExtension("Drupal\\webprofiler\\Twig\\Extension\\ProfilerExtension");
        $__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453->enter($__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "blazy_content"));

        // line 69
        echo "    ";
        if (($context["media_attributes"] ?? null)) {
            echo "<div";
            echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, ($context["media_attributes"] ?? null), "html", null, true));
            echo ">";
        }
        // line 70
        echo "      ";
        if ((($context["url"] ?? null) &&  !$this->getAttribute(($context["settings"] ?? null), "player", []))) {
            // line 71
            echo "        <a href=\"";
            echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, ($context["url"] ?? null), "html", null, true));
            echo "\"";
            echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, ($context["url_attributes"] ?? null), "html", null, true));
            echo ">";
            echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, ($context["media"] ?? null), "html", null, true));
            echo "</a>

        ";
            // line 74
            echo "        ";
            if ((($context["captions"] ?? null) &&  !twig_test_empty($this->getAttribute(($context["captions"] ?? null), "lightbox", [])))) {
                // line 75
                echo "          <div class=\"litebox-caption visually-hidden\">";
                // line 76
                echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute(($context["captions"] ?? null), "lightbox", []), "html", null, true));
                // line 77
                echo "</div>
        ";
            }
            // line 79
            echo "
      ";
        } else {
            // line 81
            echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, ($context["media"] ?? null), "html", null, true));
        }
        // line 83
        echo "    ";
        if (($context["media_attributes"] ?? null)) {
            echo "</div>";
        }
        // line 84
        echo "  ";
        
        $__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453->leave($__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453_prof);

    }

    // line 87
    public function block_blazy_caption($context, array $blocks = [])
    {
        $__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453 = $this->env->getExtension("Drupal\\webprofiler\\Twig\\Extension\\ProfilerExtension");
        $__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453->enter($__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "blazy_caption"));

        // line 88
        echo "      <div";
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, ($context["caption_attributes"] ?? null), "html", null, true));
        echo ">
        ";
        // line 89
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["captions"] ?? null), "inline", []));
        foreach ($context['_seq'] as $context["_key"] => $context["caption"]) {
            // line 90
            echo "          ";
            if ($this->getAttribute($context["caption"], "content", [])) {
                // line 91
                echo "            <";
                echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute($context["caption"], "tag", []), "html", null, true));
                echo " ";
                echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute($context["caption"], "attributes", []), "html", null, true));
                echo ">";
                echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute($context["caption"], "content", []), "html", null, true));
                echo "</";
                echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute($context["caption"], "tag", []), "html", null, true));
                echo ">
          ";
            }
            // line 93
            echo "        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['caption'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 94
        echo "      </div>
    ";
        
        $__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453->leave($__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453_prof);

    }

    public function getTemplateName()
    {
        return "modules/contrib/blazy/templates/blazy.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  282 => 94,  276 => 93,  264 => 91,  261 => 90,  257 => 89,  252 => 88,  246 => 87,  239 => 84,  234 => 83,  231 => 81,  227 => 79,  223 => 77,  221 => 76,  219 => 75,  216 => 74,  206 => 71,  203 => 70,  196 => 69,  190 => 68,  180 => 62,  176 => 61,  172 => 60,  167 => 59,  161 => 58,  154 => 53,  149 => 51,  146 => 50,  144 => 49,  139 => 48,  133 => 47,  122 => 104,  116 => 101,  111 => 100,  109 => 99,  106 => 98,  102 => 96,  99 => 87,  97 => 86,  94 => 85,  91 => 68,  89 => 67,  86 => 66,  82 => 58,  80 => 57,  77 => 56,  73 => 54,  70 => 47,  67 => 46,  65 => 45,  62 => 44,  60 => 41,  59 => 39,  57 => 35,  56 => 34,  55 => 33,  54 => 32,  53 => 31,  52 => 30,  51 => 29,  50 => 27,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "modules/contrib/blazy/templates/blazy.html.twig", "/var/www/html/saretec/web/modules/contrib/blazy/templates/blazy.html.twig");
    }
}
