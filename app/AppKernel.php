<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new JMS\AopBundle\JMSAopBundle(),
            new JMS\DiExtraBundle\JMSDiExtraBundle($this),
            new JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),
            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new Sonata\CoreBundle\SonataCoreBundle(),
            new Sonata\BlockBundle\SonataBlockBundle(),
            new Sonata\CacheBundle\SonataCacheBundle(),
            new Sonata\jQueryBundle\SonatajQueryBundle(),
            new Sonata\AdminBundle\SonataAdminBundle(),
            new Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),
            new Craue\FormFlowBundle\CraueFormFlowBundle(),
            new Mopa\Bundle\BootstrapBundle\MopaBootstrapBundle(),
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new Liip\ThemeBundle\LiipThemeBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new Genemu\Bundle\FormBundle\GenemuFormBundle(),
            new Sonata\UserBundle\SonataUserBundle(),
            new FOS\RestBundle\FOSRestBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new TSK\ProgramBundle\TSKProgramBundle(),
            new TSK\UserBundle\TSKUserBundle(),
            new TSK\ClassBundle\TSKClassBundle(),
            new TSK\SchoolBundle\TSKSchoolBundle(),
            new TSK\StudentBundle\TSKStudentBundle(),
            new TSK\InstructorBundle\TSKInstructorBundle(),
            new TSK\BilleeBundle\TSKBilleeBundle(),
            new TSK\ScheduleBundle\TSKScheduleBundle(),
            new TSK\PaymentBundle\TSKPaymentBundle(),
            new TSK\RankBundle\TSKRankBundle(),
            new TSK\ContractBundle\TSKContractBundle(),
            new TSK\RecurringPaymentBundle\TSKRecurringPaymentBundle(),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            // new Shtumi\UsefulBundle\ShtumiUsefulBundle(),
            new RaulFraile\Bundle\LadybugBundle\RaulFraileLadybugBundle(),
            new Ob\HighchartsBundle\ObHighchartsBundle(),
            new Ps\PdfBundle\PsPdfBundle(),
            new WhiteOctober\PagerfantaBundle\WhiteOctoberPagerfantaBundle(),
            new ADesigns\CalendarBundle\ADesignsCalendarBundle(),
            new VinceT\BaseBundle\VinceTBaseBundle(),
            new VinceT\AdminBundle\VinceTAdminBundle('SonataAdminBundle'),
            new VinceT\BootstrapFormBundle\VinceTBootstrapFormBundle(),
            // new Trsteel\CkeditorBundle\TrsteelCkeditorBundle(),
            new VinceT\AdminConfigurationBundle\VinceTAdminConfigurationBundle(),
            new APY\DataGridBundle\APYDataGridBundle(),
            new TSK\RulerBundle\TSKRulerBundle(),
            new Misd\PhoneNumberBundle\MisdPhoneNumberBundle()
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            # $bundles[] = new Acme\DemoBundle\AcmeDemoBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
            // $bundles[] = new WhiteOctober\PagerfantaBundle\WhiteOctoberPagerfantaBundle();
            // $bundles[] = new Knp\Bundle\MenuBundle\KnpMenuBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
