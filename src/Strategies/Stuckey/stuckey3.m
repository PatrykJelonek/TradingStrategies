%stuckey s.125(Joe Krutsinger) A.Wilinski (C)2018

%v1 koncepcja
%v2 dok�adnie wg Stuckey r=0.5
%v3 SL

clear all

FW20WS191018; %[date OHLC Vol LOP]

m=size(C);
r=0.5;
ld=0; %liczba dlugich
ls=0;
rec=-1111;
spread=1;
SL=50;
beg=3900;


ld=0; %liczba dlugich
ls=0;
ll=0;
sl=0;
for i=beg:m(1)-1
    ll=ll+1;
    zl(i)=0;
    zs(i)=0;
    range(i)=C(i-1,3)-C(i-1,4);
    //%if i>beg+100
    Rang(i)=mean(range(i-5:i));
    stop1(i)=C(i,2)+r*Rang(i);

    if C(i,3)>stop1(i)
        ld=ld+1;
        zl(i)=C(i,5)-stop1(i)-spread;
    end
    if zl(i)<-SL
        zl(i)=-SL-spread;
        sl=sl+1;
    end
    stop2(i)=C(i,2)-r*Rang(i);
    if C(i,4)<stop2(i)
        ls=ls+1;
        zs(i)=stop2(i)-C(i,5)-spread;
    end
    if zs(i)<-SL
        zs(i)=-SL-spread;
        sl=sl+1;
    end
end




zsl=cumsum(zl);
zss=cumsum(zs);
zcum=zsl+zss;

if zcum(end)>rec
    rec=zcum(end);
    paropt=[r ll ld ls sl];
    zr=zcum;
    zlr=zsl;
    zsr=zss;

end


%obl CR dl rekordowego wyniku

mdd=0;
mm=size(zr);

for j=1:mm(2)
    obni(j)=0;
    mloc(j)=max(zr(1:j));
    if zr(j)<mloc(j)
        obni(j)=mloc(j)-zr(j);
    end
end

mdd=max(obni);
calmar=zr(end)/mdd


paropt
rec

x=beg:mm(2);
mx=size(x);


figure(1)
plot(x,zlr(mm(2)-mx(2)+1:mm(2)),'-g')
hold on


plot(x,zsr(mm(2)-mx(2)+1:mm(2)),'-r')

hold on
%plot(zr)
plot(x,zr(mm(2)-mx(2)+1:mm(2)),'-b')
