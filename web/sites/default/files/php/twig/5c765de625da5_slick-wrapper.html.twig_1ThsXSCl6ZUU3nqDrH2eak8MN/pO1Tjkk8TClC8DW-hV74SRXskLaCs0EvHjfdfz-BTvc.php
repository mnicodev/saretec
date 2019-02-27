<?php

/* modules/contrib/slick/templates/slick-wrapper.html.twig */
class __TwigTemplate_16bce92cc50f7544a182c704f865747bc405aa773b7243c5e4be238326c5cb70 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453 = $this->env->getExtension("Drupal\\webprofiler\\Twig\\Extension\\ProfilerExtension");
        $__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453->enter($__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "modules/contrib/slick/templates/slick-wrapper.html.twig"));

        $tags = ["set" => 16, "for" => 30, "spaceless" => 35, "if" => 36];
        $filters = ["clean_class" => 19, "replace" => 25, "without" => 37];
        $functions = [];

        try {
            $this->env->getExtension('Twig_Extension_Sandbox')->checkSecurity(
                ['set', 'for', 'spaceless', 'if'],
                ['clean_class', 'replace', 'without'],
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

        // line 16
        $context["classes"] = [0 => "slick-wrapper", 1 => (($this->getAttribute(        // line 18
($context["settings"] ?? null), "nav", [])) ? ("slick-wrapper--asnavfor") : ("")), 2 => (($this->getAttribute(        // line 19
($context["settings"] ?? null), "skin", [])) ? (("slick-wrapper--" . \Drupal\Component\Utility\Html::getClass($this->getAttribute(($context["settings"] ?? null), "skin", [])))) : ("")), 3 => (($this->getAttribute(        // line 20
($context["settings"] ?? null), "skin_thumbnail", [])) ? (("slick-wrapper--" . \Drupal\Component\Utility\Html::getClass($this->getAttribute(($context["settings"] ?? null), "skin_thumbnail", [])))) : ("")), 4 => (($this->getAttribute(        // line 21
($context["settings"] ?? null), "vertical", [])) ? ("slick-wrapper--v") : ("")), 5 => (($this->getAttribute(        // line 22
($context["settings"] ?? null), "vertical_tn", [])) ? ("slick-wrapper--v-tn") : ("")), 6 => (($this->getAttribute(        // line 23
($context["settings"] ?? null), "thumbnail_position", [])) ? (("slick-wrapper--tn-" . \Drupal\Component\Utility\Html::getClass($this->getAttribute(($context["settings"] ?? null), "thumbnail_position", [])))) : ("")), 7 => ((twig_in_filter("over", $this->getAttribute(        // line 24
($context["settings"] ?? null), "thumbnail_position", []))) ? ("slick-wrapper--tn-overlay") : ("")), 8 => ((twig_in_filter("over", $this->getAttribute(        // line 25
($context["settings"] ?? null), "thumbnail_position", []))) ? (("slick-wrapper--tn-" . twig_replace_filter($this->getAttribute(($context["settings"] ?? null), "thumbnail_position", []), ["over-" => ""]))) : (""))];
        // line 28
        echo "
";
        // line 29
        ob_start();
        // line 30
        echo "  ";
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["items"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["item"]) {
            // line 31
            echo "    ";
            echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $context["item"], "html", null, true));
            echo "
  ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['item'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        $context["content"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 34
        echo "
";
        // line 35
        ob_start();
        // line 36
        echo "  ";
        if ($this->getAttribute(($context["settings"] ?? null), "nav", [])) {
            // line 37
            echo "    <div";
            echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, twig_without($this->getAttribute(($context["attributes"] ?? null), "addClass", [0 => ($context["classes"] ?? null)], "method"), "id"), "html", null, true));
            echo ">
      ";
            // line 38
            echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, ($context["content"] ?? null), "html", null, true));
            echo "
    </div>
  ";
        } else {
            // line 41
            echo "    ";
            echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, ($context["content"] ?? null), "html", null, true));
            echo "
  ";
        }
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
        
        $__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453->leave($__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453_prof);

    }

    public function getTemplateName()
    {
        return "modules/contrib/slick/templates/slick-wrapper.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  95 => 41,  89 => 38,  84 => 37,  81 => 36,  79 => 35,  76 => 34,  66 => 31,  61 => 30,  59 => 29,  56 => 28,  54 => 25,  53 => 24,  52 => 23,  51 => 22,  50 => 21,  49 => 20,  48 => 19,  47 => 18,  46 => 16,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "modules/contrib/slick/templates/slick-wrapper.html.twig", "/var/www/html/saretec/web/modules/contrib/slick/templates/slick-wrapper.html.twig");
    }
}
